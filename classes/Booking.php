<?php
// Member <-> class bookings.
class Booking extends Model
{
    public function isBooked(int $classId, int $memberId): bool
    {
        return $this->db->value("SELECT COUNT(*) FROM bookings WHERE class_id = ? AND member_id = ?", [$classId, $memberId]) > 0;
    }

    /** Ids of every class the member booked. */
    public function classIdsFor(int $memberId): array
    {
        return array_column($this->db->fetchAll("SELECT class_id FROM bookings WHERE member_id = ?", [$memberId]), 'class_id');
    }

    /** Returns an error message, or null when the booking was made. */
    public function book(int $classId, int $memberId): ?string
    {
        // Lock the class row so two members cannot take the last seat together
        $this->db->begin();
        $class = $this->db->fetch("SELECT capacity FROM classes WHERE id = ? FOR UPDATE", [$classId]);
        $taken = (int)$this->db->value("SELECT COUNT(*) FROM bookings WHERE class_id = ?", [$classId]);

        if (!$class || $taken >= $class['capacity']) {
            $this->db->rollBack();
            return 'Sorry, this class is full.';
        }
        $this->db->query("INSERT INTO bookings (class_id, member_id) VALUES (?,?)", [$classId, $memberId]);
        $this->db->commit();
        return null;
    }

    public function cancel(int $classId, int $memberId): void
    {
        $this->db->query("DELETE FROM bookings WHERE class_id = ? AND member_id = ?", [$classId, $memberId]);
    }

    /** Members who booked a class (trainer view), optionally filtered by name/email. */
    public function members(int $classId, string $search = ''): array
    {
        return $this->db->fetchAll(
            "SELECT u.full_name, u.email, u.phone, b.booked_at FROM bookings b
             JOIN users u ON u.id = b.member_id
             WHERE b.class_id = ? AND (u.full_name LIKE ? OR u.email LIKE ?) ORDER BY b.booked_at",
            [$classId, "%$search%", "%$search%"]
        );
    }

    public function total(): int
    {
        return (int)$this->db->value("SELECT COUNT(*) FROM bookings");
    }
}
