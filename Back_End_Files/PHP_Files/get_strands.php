<?php
header('Content-Type: application/json');
include "../../DB_Connection/Connection.php";

try {
    /*
     * Preferred query for updated schema (track_name + is_active).
     * Falls back to legacy schema below if columns are not yet migrated.
     */
    $stmt = $connection->prepare("
        SELECT strand_id, strand_abbreviation, strand_name, track_name
        FROM strands
        WHERE is_active = 1
        ORDER BY track_name, strand_name
    ");

    $strands = [];
    if ($stmt && $stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $strands[] = [
                'strand_id' => (int)$row['strand_id'],
                'strand_name' => $row['strand_name'],
                'strand_abbreviation' => $row['strand_abbreviation'],
                'track_name' => $row['track_name']
            ];
        }
        echo json_encode($strands);
        exit;
    }

    // Legacy fallback: map old strand labels to the new 2-track, 6-strand model.
    $legacyStmt = $connection->prepare("
        SELECT strand_id, strand_abbreviation, strand_name
        FROM strands
        ORDER BY strand_name
    ");
    $legacyStmt->execute();
    $legacyResult = $legacyStmt->get_result();

    $map = [
        'STEM' => ['strand_name' => 'Science, Technology, Engineering and Mathematics', 'track_name' => 'Academic Track'],
        'ABM' => ['strand_name' => 'Business and Entrepreneurship', 'track_name' => 'Academic Track'],
        'BE' => ['strand_name' => 'Business and Entrepreneurship', 'track_name' => 'Academic Track'],
        'HUMSS' => ['strand_name' => 'Arts, Social Sciences, and Humanities', 'track_name' => 'Academic Track'],
        'ASSH' => ['strand_name' => 'Arts, Social Sciences, and Humanities', 'track_name' => 'Academic Track'],
        'TVL-ICT' => ['strand_name' => 'Information and Communication Technology', 'track_name' => 'Technical Professional (TechPro) Track'],
        'ICT' => ['strand_name' => 'Information and Communication Technology', 'track_name' => 'Technical Professional (TechPro) Track'],
        'TVL-EIM' => ['strand_name' => 'Industrial Arts', 'track_name' => 'Technical Professional (TechPro) Track'],
        'IA' => ['strand_name' => 'Industrial Arts', 'track_name' => 'Technical Professional (TechPro) Track'],
        'TVL-HE' => ['strand_name' => 'Family and Consumer Science', 'track_name' => 'Technical Professional (TechPro) Track'],
        'FCS' => ['strand_name' => 'Family and Consumer Science', 'track_name' => 'Technical Professional (TechPro) Track'],
    ];

    while ($row = $legacyResult->fetch_assoc()) {
        $abbr = strtoupper(trim($row['strand_abbreviation']));
        if (!isset($map[$abbr])) {
            continue; // Exclude legacy strands outside the updated 6 strands (e.g. GAS).
        }

        $strands[] = [
            'strand_id' => (int)$row['strand_id'],
            'strand_name' => $map[$abbr]['strand_name'],
            'strand_abbreviation' => $abbr,
            'track_name' => $map[$abbr]['track_name']
        ];
    }

    usort($strands, function ($a, $b) {
        return strcmp($a['track_name'] . $a['strand_name'], $b['track_name'] . $b['strand_name']);
    });

    echo json_encode($strands);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
