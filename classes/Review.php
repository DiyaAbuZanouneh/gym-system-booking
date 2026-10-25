<?php
// Gym reviews: written by members/trainers, shown on Home only if the admin approves.
class Review extends Model
{
    public function create(int $userId, int $rating, string $comment): bool
    {
        if ($rating < 1 || $rating > 5 || $comment === '') return false;
        $this->db->query("INSERT INTO reviews (user_id, rating, comment) VALUES (?,?,?)", [$userId, $rating, $comment]);
        return true;
    }

    public function visible(int $limit = 6): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, u.full_name, u.role FROM reviews r JOIN users u ON u.id = r.user_id
             WHERE r.is_visible = 1 ORDER BY r.created_at DESC LIMIT " . (int)$limit
        );
    }

    /** $tab: all | pending | visible */
    public function list(string $tab = 'all'): array
    {
        $where = $tab === 'pending' ? 'WHERE r.is_visible = 0' : ($tab === 'visible' ? 'WHERE r.is_visible = 1' : '');
        return $this->db->fetchAll(
            "SELECT r.*, u.full_name, u.role FROM reviews r JOIN users u ON u.id = r.user_id $where
             ORDER BY r.created_at DESC, r.id DESC"
        );
    }

    public function latest(int $limit = 4): array
    {
        return array_slice($this->list('all'), 0, $limit);
    }

    public function toggleVisible(int $id): void
    {
        $this->db->query("UPDATE reviews SET is_visible = 1 - is_visible WHERE id = ?", [$id]);
    }

    public function delete(int $id): void
    {
        $this->db->query("DELETE FROM reviews WHERE id = ?", [$id]);
    }
}
