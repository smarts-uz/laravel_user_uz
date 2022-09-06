<?php

if ($amount >= setting('admin.min_amount') ?? 4000) {
    return true;
} else {
    return false;
}
