<?php
// Read-only lookup data: class types (filters/forms) and Home page feature cards.
class Catalog extends Model
{
    public function classTypes(): array
    {
        return $this->db->fetchAll("SELECT * FROM class_types ORDER BY name");
    }

    public function features(): array
    {
        return $this->db->fetchAll("SELECT * FROM gym_features ORDER BY sort_order");
    }
}
