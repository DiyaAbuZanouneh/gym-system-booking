<?php
// Numbers for the admin dashboard.
class Stats extends Model
{
    public function summary(): array
    {
        return $this->db->fetch("SELECT
            (SELECT COUNT(*) FROM users WHERE role='member' AND is_active=1) AS members_active,
            (SELECT COUNT(*) FROM users WHERE role='member' AND is_active=0) AS members_inactive,
            (SELECT COUNT(*) FROM users WHERE role='trainer') AS trainers,
            (SELECT COUNT(*) FROM classes WHERE class_date >= CURDATE()) AS upcoming,
            (SELECT COUNT(*) FROM bookings) AS bookings,
            (SELECT COUNT(*) FROM reviews WHERE is_visible = 0) AS pending");
    }
}
