<?php
// 檔名: merge_reports.php

// 讀取 CSV 檔案的函數
function readCSV($filename) {
    $data = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}

// 將資料寫入 CSV 檔案的函數
function writeCSV($filename, $data) {
    if (($handle = fopen($filename, 'w')) !== FALSE) {
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}

// 讀取兩個 CSV 檔案
$data1 = readCSV("總報表_button.csv"); // 從按鈕生成的 CSV 檔案，轉換自 Excel
$data2 = readCSV("generate_exit_time_report.csv"); // 從 generate_exit_time_report.php 生成的 CSV 檔案

// 建立合併後的資料陣列，並加入標題
$mergedData = [$data1[0]]; // 假設第一行是標題
array_shift($data1); // 移除標題行
array_shift($data2); // 移除標題行
$mergedData = array_merge($mergedData, $data1);

// 合併第二份資料，根據車牌號碼判斷是否存在相同的資料
foreach ($data2 as $row2) {
    $licensePlate2 = $row2[0]; // 假設車牌號碼位於第 1 列 (索引為 0)
    $found = false;

    // 尋找相同車牌號碼的行
    foreach ($mergedData as $key => $row1) {
        if ($key == 0) continue; // 跳過標題行

        $licensePlate1 = $row1[6]; // 假設車牌號碼位於第 7 欄 (索引為 6)

        if ($licensePlate1 == $licensePlate2) {
            // 如果車牌號碼相同，合併資料
            for ($i = 0; $i < count($row2); $i++) {
                if (empty($row1[$i]) && !empty($row2[$i])) {
                    $mergedData[$key][$i] = $row2[$i];
                }
            }
            $found = true;
            break;
        }
    }

    // 如果沒有找到相同車牌號碼，則將這行資料新增到合併資料中
    if (!$found) {
        $mergedData[] = $row2;
    }
}

// 將合併後的資料寫入新的 CSV 檔案
writeCSV("合併後的總報表.csv", $mergedData);

echo "合併完成，已生成合併後的總報表.csv";
?>
