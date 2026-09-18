<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);
$range = (string)($_GET['range'] ?? '24h');

$allowedRanges = [
    '1h',
    '6h',
    '24h',
    'today',
    '7d',
    'all'
];

if (!in_array($range, $allowedRanges, true)) {
    $range = '24h';
}

if ($id <= 0) {
    http_response_code(400);
    exit('Invalid device.');
}

$stmt = $pdo->prepare("
    SELECT
        d.id,
        d.device_uuid,
        d.device_name,
        d.device_type,
        u.full_name AS owner_name
    FROM devices d
    LEFT JOIN users u
        ON u.id = d.owner_id
    WHERE d.id = ?
        AND d.deleted_at IS NULL
    LIMIT 1
");

$stmt->execute([$id]);
$device = $stmt->fetch();

if (!$device) {
    http_response_code(404);
    exit('Device not found.');
}

$where = 'device_id = :device_id';
$params = [
    ':device_id' => $id
];

switch ($range) {
    case '1h':
        $where .= ' AND created_at >= NOW() - INTERVAL 1 HOUR';
        break;

    case '6h':
        $where .= ' AND created_at >= NOW() - INTERVAL 6 HOUR';
        break;

    case '24h':
        $where .= ' AND created_at >= NOW() - INTERVAL 24 HOUR';
        break;

    case 'today':
        $where .= ' AND created_at >= CURDATE()';
        break;

    case '7d':
        $where .= ' AND created_at >= NOW() - INTERVAL 7 DAY';
        break;

    case 'all':
        break;
}

$stmt = $pdo->prepare("
    SELECT
        id,
        value,
        created_at
    FROM device_logs
    WHERE {$where}
    ORDER BY id ASC
");

$stmt->execute($params);
$logs = $stmt->fetchAll();

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    exit('Excel export requires the PHP ZipArchive extension.');
}

function excelColumnName(int $number): string
{
    $name = '';

    while ($number > 0) {
        $remainder = ($number - 1) % 26;
        $name = chr(65 + $remainder) . $name;
        $number = intdiv($number - 1, 26);
    }

    return $name;
}

function excelCell(string $value, string $type = 'string'): string
{
    $escaped = htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

    return '<c t="' . $type . '"><v>' . $escaped . '</v></c>';
}

$rows = [];

$rows[] = [
    'Device Name',
    (string)$device['device_name'],
    'Device UUID',
    (string)$device['device_uuid'],
    'Device Type',
    (string)$device['device_type'],
    'Owner',
    (string)($device['owner_name'] ?? '-'),
    'Export Range',
    strtoupper($range)
];

$rows[] = [];

$rows[] = [
    'Record ID',
    'Value',
    'Received Time'
];

foreach ($logs as $log) {
    $rows[] = [
        (string)$log['id'],
        (string)$log['value'],
        (string)$log['created_at']
    ];
}

$sheetRows = '';

foreach ($rows as $rowIndex => $row) {
    $excelRow = $rowIndex + 1;
    $cells = '';

    foreach ($row as $columnIndex => $value) {
        $column = excelColumnName($columnIndex + 1);
        $cellType = 'inlineStr';
        $escaped = htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $cells .= '<c r="' . $column . $excelRow . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
    }

    $sheetRows .= '<row r="' . $excelRow . '">' . $cells . '</row>';
}

$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
'
    . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
    . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<dimension ref="A1:H' . max(1, count($rows)) . '"/>'
    . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
    . '<sheetData>' . $sheetRows . '</sheetData>'
    . '</worksheet>';

$workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
'
    . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
    . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<sheets><sheet name="Device Data" sheetId="1" r:id="rId1"/></sheets>'
    . '</workbook>';

$relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
    . '</Relationships>';

$workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
    . '</Relationships>';

$contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
    . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
    . '</Types>';

$tmpFile = tempnam(sys_get_temp_dir(), 'mceiot_xlsx_');

$zip = new ZipArchive();

if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('Unable to create Excel file.');
}

$zip->addFromString('[Content_Types].xml', $contentTypesXml);
$zip->addFromString('_rels/.rels', $relsXml);
$zip->addFromString('xl/workbook.xml', $workbookXml);
$zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
$zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
$zip->close();

$safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string)$device['device_name']);
$safeName = trim((string)$safeName, '_');

if ($safeName === '') {
    $safeName = 'device';
}

$filename = $safeName . '_' . $range . '_data_' . date('Y-m-d_H-i-s') . '.xlsx';

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: max-age=0, must-revalidate');
header('Pragma: public');

readfile($tmpFile);
unlink($tmpFile);
exit;
