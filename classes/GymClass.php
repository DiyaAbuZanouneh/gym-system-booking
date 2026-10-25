<?php
// Classes created by trainers (table: classes + class_types).
class GymClass extends Model
{
    // Class row + type, trainer, booked count and average rating
    private const BASE_SQL = "SELECT c.*, ct.name AS type_name, ct.image AS type_image, u.full_name AS trainer_name,
        (SELECT COUNT(*) FROM bookings b WHERE b.class_id = c.id) AS booked,
        (SELECT ROUND(AVG(r.rating),1) FROM class_ratings r WHERE r.class_id = c.id) AS avg_rating
        FROM classes c
        JOIN class_types ct ON ct.id = c.class_type_id
        JOIN users u ON u.id = c.trainer_id";

    public function find(int $id): ?array
    {
        return $this->db->fetch(self::BASE_SQL . " WHERE c.id = ?", [$id]);
    }

    public function findForTrainer(int $id, int $trainerId): ?array
    {
        return $this->db->fetch(self::BASE_SQL . " WHERE c.id = ? AND c.trainer_id = ?", [$id, $trainerId]);
    }

    /** Upcoming classes with optional filters: type (id), date (Y-m-d), tod (morning|afternoon|evening). */
    public function search(int $type = 0, string $date = '', string $tod = ''): array
    {
        $where = ["c.class_date >= CURDATE()"];
        $params = [];
        if ($type) { $where[] = "c.class_type_id = ?"; $params[] = $type; }
        if ($date) { $where[] = "c.class_date = ?";    $params[] = $date; }
        if ($tod === 'morning')   $where[] = "c.start_time < '12:00'";
        if ($tod === 'afternoon') $where[] = "c.start_time >= '12:00' AND c.start_time < '17:00'";
        if ($tod === 'evening')   $where[] = "c.start_time >= '17:00'";

        return $this->db->fetchAll(self::BASE_SQL . " WHERE " . implode(' AND ', $where) . " ORDER BY c.class_date, c.start_time", $params);
    }

    public function forTrainer(int $trainerId, bool $upcoming): array
    {
        $op = $upcoming ? '>=' : '<';
        return $this->db->fetchAll(
            self::BASE_SQL . " WHERE c.trainer_id = ? AND c.class_date $op CURDATE() ORDER BY c.class_date, c.start_time",
            [$trainerId]
        );
    }

    public function forMember(int $memberId): array
    {
        return $this->db->fetchAll(
            self::BASE_SQL . " JOIN bookings mb ON mb.class_id = c.id WHERE mb.member_id = ? ORDER BY c.class_date DESC, c.start_time",
            [$memberId]
        );
    }

    public function all(): array
    {
        return $this->db->fetchAll(self::BASE_SQL . " ORDER BY c.class_date DESC, c.start_time");
    }

    public function topBooked(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT c.title, c.capacity, COUNT(b.id) AS n FROM classes c LEFT JOIN bookings b ON b.class_id = c.id
             GROUP BY c.id ORDER BY n DESC LIMIT " . (int)$limit
        );
    }

    public function trainerSpecialization(int $trainerId): string
    {
        return (string)$this->db->value("SELECT specialization FROM trainer_details WHERE user_id = ?", [$trainerId]);
    }

    /** Returns a list of error messages (empty = valid), including the time-conflict rule. */
    public function validate(array $d, int $trainerId): array
    {
        $errors = [];
        if ($d['title'] === '' || !$d['class_type_id'] || !$d['class_date'] || !$d['start_time'] || !$d['end_time']) {
            $errors[] = 'Please fill in all required fields.';
        }
        if ($d['capacity'] < 1)                    $errors[] = 'Capacity must be greater than 0.';
        if ($d['end_time'] <= $d['start_time'])    $errors[] = 'End time must be after start time.';
        if ($d['class_date'] < date('Y-m-d'))      $errors[] = 'The date cannot be in the past.';

        if (!$errors && ($o = $this->findConflict($trainerId, $d['class_date'], $d['start_time'], $d['end_time']))) {
            $errors[] = "This time overlaps with your class '{$o['title']}' (" . substr($o['start_time'], 0, 5) . '–' . substr($o['end_time'], 0, 5) . '). Choose another time.';
        }
        return $errors;
    }

    public function findConflict(int $trainerId, string $date, string $start, string $end): ?array
    {
        return $this->db->fetch(
            "SELECT title, start_time, end_time FROM classes
             WHERE trainer_id = ? AND class_date = ? AND ? < end_time AND ? > start_time LIMIT 1",
            [$trainerId, $date, $start, $end]
        );
    }

    public function create(int $trainerId, array $d): int
    {
        $this->db->query(
            "INSERT INTO classes (trainer_id, class_type_id, title, description, image, class_date, start_time, end_time, capacity, room)
             VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$trainerId, $d['class_type_id'], $d['title'], $d['description'], $d['image'] ?: null,
             $d['class_date'], $d['start_time'], $d['end_time'], $d['capacity'], $d['room']]
        );
        return $this->db->lastId();
    }

    /** Bookings and ratings go with it (ON DELETE CASCADE). $trainerId = null means admin (any class). */
    public function delete(int $id, ?int $trainerId = null): void
    {
        if ($trainerId === null) {
            $this->db->query("DELETE FROM classes WHERE id = ?", [$id]);
        } else {
            $this->db->query("DELETE FROM classes WHERE id = ? AND trainer_id = ?", [$id, $trainerId]);
        }
    }

    public function upcomingCount(): int
    {
        return (int)$this->db->value("SELECT COUNT(*) FROM classes WHERE class_date >= CURDATE()");
    }
}
