<?php

namespace App\Services;

use ZipArchive;
use SimpleXMLElement;
use Carbon\Carbon;

class SpreadsheetService
{
    /**
     * Header standar template Admin Fakultas Teknik UIM:
     * Nama, NIM, Prodi, dan Nilai dari Dosen Pembimbing
     */
    public const HEADERS = [
        'NIM',
        'NAMA',
        'PRODI',
        'NILAI_DOSEN_PEMBIMBING',
    ];

    /**
     * Membuat file template XLSX resmi Fakultas Teknik UIM
     */
    public function generateTemplate(string $outputPath, ?array $customRows = null): bool
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $headers = self::HEADERS;
        $sampleRows = $customRows ?? [
            [
                '2023020100066',
                'Moh. Hasan',
                'Teknik Informatika',
                '90.2',
            ],
            [
                '2023020100068',
                'Faruq',
                'Teknik Informatika',
                '90.2',
            ],
            [
                '2023020200006',
                'Ilham Maulana',
                'Sistem Informasi',
                '93.83',
            ],
            [
                '2023020100104',
                'Hermanto',
                'Teknik Informatika',
                '83.26',
            ],
            [
                '2023020100023',
                'Ahmad Dahlan',
                'Teknik Informatika',
                '54.3',
            ],
        ];

        return $this->createXlsx($outputPath, $headers, $sampleRows);
    }

    /**
     * Membaca dan mem-parsing file XLSX atau CSV yang diunggah
     */
    public function parseFile(string $filePath, string $originalExtension = ''): array
    {
        $ext = strtolower($originalExtension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['csv', 'txt'])) {
            $rows = $this->parseCsv($filePath);
        } else {
            // Default as XLSX
            $rows = $this->readXlsx($filePath);
            if (empty($rows)) {
                $rows = $this->parseCsv($filePath);
            }
        }

        return $this->mapRowsToAssociative($rows);
    }

    /**
     * Generator file OpenXML XLSX
     */
    public function createXlsx(string $filePath, array $headers, array $rows): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $allStrings = [];
        $stringIndex = [];

        $getStringId = function ($str) use (&$allStrings, &$stringIndex) {
            $str = (string) $str;
            if (isset($stringIndex[$str])) {
                return $stringIndex[$str];
            }
            $idx = count($allStrings);
            $stringIndex[$str] = $idx;
            $allStrings[] = $str;
            return $idx;
        };

        // Header string indices
        $headerIndices = [];
        foreach ($headers as $h) {
            $headerIndices[] = $getStringId($h);
        }

        // Sheet rows XML
        $sheetDataXml = "<sheetData>";

        // Row 1: Headers
        $sheetDataXml .= '<row r="1" spans="1:' . count($headers) . '">';
        foreach ($headerIndices as $colIdx => $sId) {
            $colLetter = $this->getColumnLetter($colIdx + 1);
            $cellRef = $colLetter . '1';
            $sheetDataXml .= '<c r="' . $cellRef . '" t="s"><v>' . $sId . '</v></c>';
        }
        $sheetDataXml .= '</row>';

        // Data Rows
        $rowNum = 2;
        foreach ($rows as $r) {
            $sheetDataXml .= '<row r="' . $rowNum . '" spans="1:' . count($headers) . '">';
            $colIdx = 0;
            foreach ($r as $val) {
                if ($colIdx >= count($headers)) break;
                $colLetter = $this->getColumnLetter($colIdx + 1);
                $cellRef = $colLetter . $rowNum;

                if (is_numeric($val) && !str_starts_with((string)$val, '0')) {
                    $sheetDataXml .= '<c r="' . $cellRef . '"><v>' . htmlspecialchars((string)$val, ENT_XML1) . '</v></c>';
                } else {
                    $sId = $getStringId($val);
                    $sheetDataXml .= '<c r="' . $cellRef . '" t="s"><v>' . $sId . '</v></c>';
                }
                $colIdx++;
            }
            $sheetDataXml .= '</row>';
            $rowNum++;
        }
        $sheetDataXml .= "</sheetData>";

        // Shared strings XML
        $sstXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sstXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($allStrings) . '" uniqueCount="' . count($allStrings) . '">';
        foreach ($allStrings as $s) {
            $sstXml .= '<si><t>' . htmlspecialchars($s, ENT_XML1) . '</t></si>';
        }
        $sstXml .= '</sst>';

        // Sheet XML
        $wsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wsXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $wsXml .= '<dimension ref="A1:' . $this->getColumnLetter(count($headers)) . ($rowNum - 1) . '"/>';
        $wsXml .= '<sheetViews><sheetView tabSelected="1" workbookViewId="0"/></sheetViews>';
        $wsXml .= '<sheetFormatPr defaultRowHeight="15"/>';
        $wsXml .= $sheetDataXml;
        $wsXml .= '</worksheet>';

        // [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
        $contentTypes .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
        $contentTypes .= '<Default Extension="xml" ContentType="application/xml"/>';
        $contentTypes .= '<Override PartName="/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';
        $contentTypes .= '<Override PartName="/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        $contentTypes .= '<Override PartName="/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';
        $contentTypes .= '</Types>';

        // _rels/.rels
        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $rootRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $rootRels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="workbook.xml"/>';
        $rootRels .= '</Relationships>';

        // workbook.xml
        $wbXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wbXml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $wbXml .= '<sheets><sheet name="Data Mahasiswa KP" sheetId="1" r:id="rId1"/></sheets>';
        $wbXml .= '</workbook>';

        // workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wbRels .= '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        $wbRels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="sheet1.xml"/>';
        $wbRels .= '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';
        $wbRels .= '</Relationships>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rootRels);
        $zip->addFromString('workbook.xml', $wbXml);
        $zip->addFromString('_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('sheet1.xml', $wsXml);
        $zip->addFromString('sharedStrings.xml', $sstXml);

        $zip->close();
        return true;
    }

    protected function colLetterToIndex(string $ref): int
    {
        $letters = preg_replace('/[0-9]/', '', $ref);
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord(strtoupper($letters[$i])) - ord('A') + 1);
        }
        return max(0, $idx - 1);
    }

    protected function getColumnLetter(int $colNumber): string
    {
        $letter = '';
        while ($colNumber > 0) {
            $mod = ($colNumber - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $colNumber = (int)(($colNumber - $mod) / 26);
        }
        return $letter;
    }

    /**
     * Membaca file XLSX
     */
    protected function readXlsx(string $filePath): array
    {
        $rows = [];
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sstXml = $zip->getFromName('xl/sharedStrings.xml') ?: $zip->getFromName('sharedStrings.xml');
        if ($sstXml) {
            $xmlObj = simplexml_load_string($sstXml);
            if ($xmlObj && isset($xmlObj->si)) {
                foreach ($xmlObj->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $txt = '';
                        foreach ($si->r as $r) {
                            $txt .= (string) $r->t;
                        }
                        $sharedStrings[] = $txt;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml') ?: $zip->getFromName('sheet1.xml');
        if ($sheetXml) {
            $xmlObj = simplexml_load_string($sheetXml);
            if ($xmlObj && isset($xmlObj->sheetData->row)) {
                foreach ($xmlObj->sheetData->row as $row) {
                    $rowData = [];
                    foreach ($row->c as $cell) {
                        $ref = (string) $cell['r'];
                        $colIdx = $this->colLetterToIndex($ref);
                        $type = (string) $cell['t'];
                        $val = (string) $cell->v;
                        if ($type === 's' && isset($sharedStrings[(int)$val])) {
                            $rowData[$colIdx] = trim($sharedStrings[(int)$val]);
                        } else {
                            $rowData[$colIdx] = trim($val);
                        }
                    }
                    if (!empty(array_filter($rowData, fn($v) => $v !== ''))) {
                        $maxKey = max(array_keys($rowData));
                        $dense = [];
                        for ($k = 0; $k <= $maxKey; $k++) {
                            $dense[$k] = $rowData[$k] ?? '';
                        }
                        $rows[] = $dense;
                    }
                }
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Membaca file CSV
     */
    protected function parseCsv(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);
            $delimiter = (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) ? ';' : ',';

            while (($data = fgetcsv($handle, 2048, $delimiter)) !== false) {
                if (!empty(array_filter($data, fn($v) => trim($v) !== ''))) {
                    $rows[] = array_map('trim', $data);
                }
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Memetakan baris template ke associative array standar:
     * ['nim', 'nama', 'prodi', 'nilai_dosen_pembimbing']
     */
    public function mapRowsToAssociative(array $rawRows): array
    {
        if (count($rawRows) < 2) {
            return [];
        }

        // Cari baris header
        $headerRowIdx = 0;
        $normalizedHeaders = [];

        foreach ($rawRows as $rIdx => $row) {
            $candidateHeaders = [];
            foreach ($row as $colIdx => $h) {
                $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$h));
                if (in_array($clean, ['nim', 'npm', 'nomorinduk', 'username', 'noinduk'])) {
                    $candidateHeaders[$colIdx] = 'nim';
                } elseif (in_array($clean, ['nama', 'namalengkap', 'namamahasiswa', 'fullname', 'studentname'])) {
                    $candidateHeaders[$colIdx] = 'nama';
                } elseif (in_array($clean, ['prodi', 'programstudi', 'jurusan', 'department'])) {
                    $candidateHeaders[$colIdx] = 'prodi';
                } elseif (in_array($clean, ['nilaidosenpembimbing', 'nilaidosen', 'nilaipembimbing', 'jumlah', 'score', 'gradescore', 'evaluasi'])) {
                    $candidateHeaders[$colIdx] = 'nilai_dosen_pembimbing';
                } elseif (in_array($clean, ['grade', 'predikat', 'huruf', 'nilaimutu'])) {
                    $candidateHeaders[$colIdx] = 'grade';
                } elseif ($clean === 'nilai') {
                    $candidateHeaders[$colIdx] = isset(array_flip($candidateHeaders)['nilai_dosen_pembimbing']) ? 'grade' : 'nilai_dosen_pembimbing';
                }
            }

            // Jika baris ini mengandung setidaknya nim/npm dan nama atau nilai, ini adalah header
            if (isset(array_flip($candidateHeaders)['nim']) || isset(array_flip($candidateHeaders)['nama'])) {
                $headerRowIdx = $rIdx;
                $normalizedHeaders = $candidateHeaders;
                break;
            }
        }

        // Fallback jika tidak terdeteksi by name
        if (empty($normalizedHeaders)) {
            $normalizedHeaders = [
                0 => 'nim',
                1 => 'nama',
                2 => 'prodi',
                3 => 'nilai_dosen_pembimbing',
            ];
            $headerRowIdx = 0;
        }

        $records = [];
        $dataRows = array_slice($rawRows, $headerRowIdx + 1);

        foreach ($dataRows as $row) {
            $record = [];
            foreach ($row as $colIdx => $val) {
                if (isset($normalizedHeaders[$colIdx])) {
                    $record[$normalizedHeaders[$colIdx]] = $val;
                }
            }

            // Deteksi kolom Prodi jika belum terpetakan dari header (misal template No, Nama, NPM, [Prodi], Jumlah, Nilai)
            if (empty($record['prodi'])) {
                if (isset($row[3]) && !empty(trim((string)$row[3]))) {
                    $record['prodi'] = $row[3];
                } else {
                    foreach ($row as $cVal) {
                        $uVal = strtoupper((string)$cVal);
                        if (str_contains($uVal, 'SISTEM') || str_contains($uVal, 'SIS') || str_contains($uVal, 'INFORMATIKA') || str_contains($uVal, 'IORMA')) {
                            $record['prodi'] = $cVal;
                            break;
                        }
                    }
                }
            }

            // Fallback jika baris memiliki format: NO., NAMA, NPM, PRODI, JUMLAH, NILAI
            if (empty($record['nim']) && count($row) >= 5) {
                // Check if column 2 looks like NIM/NPM
                $candidateNim = preg_replace('/[^0-9]/', '', str_replace(['«', '®'], ['3', '8'], (string)($row[2] ?? '')));
                if (strlen($candidateNim) >= 8) {
                    $record['nim'] = $candidateNim;
                    $record['nama'] = $row[1] ?? '';
                    $record['prodi'] = $row[3] ?? '';
                    $record['nilai_dosen_pembimbing'] = (float) ($row[4] ?? 0);
                }
            }

            // Ambil nilai angka jika nilai_dosen_pembimbing belum terisi atau 0 tapi kolom ke-4 (JUMLAH) ada
            if ((empty($record['nilai_dosen_pembimbing']) || $record['nilai_dosen_pembimbing'] == 0) && isset($row[4]) && is_numeric($row[4])) {
                $record['nilai_dosen_pembimbing'] = (float) $row[4];
            }

            if (!empty($record['nim'])) {
                // Bersihkan OCR artifacts pada NIM
                $cleanNim = trim((string)$record['nim']);
                $cleanNim = str_replace(['«', '®'], ['3', '8'], $cleanNim);
                $cleanNim = preg_replace('/[^0-9]/', '', $cleanNim);

                // Bersihkan Nama
                $cleanNama = !empty($record['nama']) ? trim((string)$record['nama']) : ('Mahasiswa ' . $cleanNim);
                $cleanNama = str_replace(['ľ', '⭲', 'í', '®', '«', '""', '"'], ['t', 'n', 'i', '', '', '', ''], $cleanNama);
                $cleanNama = preg_replace('/\s+/', ' ', $cleanNama);
                $cleanNama = ucwords(strtolower(trim($cleanNama)));

                // Bersihkan & Standarkan Prodi (Teknik Informatika vs Sistem Informasi)
                $cleanProdiUpper = strtoupper(trim((string)($record['prodi'] ?? '')));
                if (str_contains($cleanProdiUpper, 'SISTEM') || str_contains($cleanProdiUpper, 'SIS') || str_contains($cleanNim, '0202')) {
                    $cleanProdi = 'Sistem Informasi';
                } else {
                    $cleanProdi = 'Teknik Informatika';
                }

                $cleanNilai = isset($record['nilai_dosen_pembimbing']) ? (float)$record['nilai_dosen_pembimbing'] : 0.0;

                $records[] = [
                    'nim' => $cleanNim,
                    'nama' => $cleanNama,
                    'prodi' => $cleanProdi,
                    'nilai_dosen_pembimbing' => $cleanNilai,
                ];
            }
        }

        return $records;
    }
}
