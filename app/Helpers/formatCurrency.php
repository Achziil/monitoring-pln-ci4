<?php

function formatCurrency($value) {
    if ($value === null || $value === '-') {
        return '-';
    }
    return "Rp " . number_format($value, 0, ',', '.');
}
