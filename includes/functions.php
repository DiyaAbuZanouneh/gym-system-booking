<?php
// Small view helpers (no database access here — that lives in classes/).
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function go($url) { header('Location: ' . $url); exit; }

function stars($n) {
    $n = (int)round($n);
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    return strtoupper(mb_substr($parts[0], 0, 1) . (isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));
}

function class_image($c) { return $c['image'] ?: $c['type_image']; }

function seats_badge($c) {
    $left = $c['capacity'] - $c['booked'];
    if ($left <= 0) return '<span class="badge badge-danger">Full</span>';
    if ($left <= 3) return '<span class="badge badge-warning">' . $left . ' seats left</span>';
    return '<span class="badge badge-success">' . $left . ' seats left</span>';
}

function fmt_time($t) { return date('g:i A', strtotime($t)); }
function fmt_date($d) { return date('D, M j', strtotime($d)); }
