<?php

namespace App\Validation;

/**
 * Lightweight input checks used by the controllers before they touch
 * persistence. Each method returns a list of human-readable error
 * messages — an empty array means the input passed.
 */
class InputValidator
{
    /**
     * Validate a conference's start_date / end_date pair.
     * Both must parse as a date and end must not be before start.
     */
    public static function validateConferenceDates(?string $startDate, ?string $endDate): array
    {
        $errors = [];
        $startDate = $startDate !== null ? trim($startDate) : '';
        $endDate = $endDate !== null ? trim($endDate) : '';
        if ($startDate === '' || $endDate === '') {
            $errors[] = 'Start date and end date are required.';
            return $errors;
        }
        try {
            $start = new \DateTimeImmutable($startDate);
            $end = new \DateTimeImmutable($endDate);
        } catch (\Exception) {
            $errors[] = 'Start date and end date must be valid dates (YYYY-MM-DD).';
            return $errors;
        }
        if ($end < $start) {
            $errors[] = 'End date must be on or after the start date.';
        }
        return $errors;
    }

    /**
     * Validate a session's start_time / end_time pair.
     * Both must parse as a datetime and end must be strictly after start.
     */
    public static function validateSessionTimes(?string $startTime, ?string $endTime): array
    {
        $errors = [];
        $startTime = $startTime !== null ? trim($startTime) : '';
        $endTime = $endTime !== null ? trim($endTime) : '';
        if ($startTime === '' || $endTime === '') {
            $errors[] = 'Start time and end time are required.';
            return $errors;
        }
        try {
            $start = new \DateTimeImmutable($startTime);
            $end = new \DateTimeImmutable($endTime);
        } catch (\Exception) {
            $errors[] = 'Start time and end time must be valid datetimes.';
            return $errors;
        }
        if ($end <= $start) {
            $errors[] = 'End time must be strictly after the start time.';
        }
        return $errors;
    }

    /**
     * Validate a capacity integer. Must be present and strictly positive.
     */
    public static function validateCapacity(mixed $capacity): array
    {
        if ($capacity === null || $capacity === '') {
            return ['Capacity is required.'];
        }
        if (!is_numeric($capacity) || (int) $capacity <= 0) {
            return ['Capacity must be a positive integer.'];
        }
        return [];
    }
}
