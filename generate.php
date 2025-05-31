<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$date = date("Y-m-d_H-i-s");
$folder = "output/$date";
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$templatePath = 'template.docx';
$savePath = "$folder/تحضير_$date.docx";

$templateProcessor = new TemplateProcessor($templatePath);

// تعويض النصوص العادية
$fields = [
    'strategy',
    'subject',
    'lesson',
    'semester',
    'grade',
    'week',
    'execDate',
    'students',
    'goals',
    'teacher',
    'principal'
];

foreach ($fields as $field) {
    $value = isset($_POST[$field]) ? htmlspecialchars($_POST[$field]) : '';
    $templateProcessor->setValue($field, $value);
}

// أسماء الصور
$imageFields = ['img1', 'img2', 'img3', 'img4'];

foreach ($imageFields as $imgKey) {
    if (isset($_FILES[$imgKey]) && $_FILES[$imgKey]['error'] == 0) {
        $imgTmpPath = $_FILES[$imgKey]['tmp_name'];
        $imgExt = pathinfo($_FILES[$imgKey]['name'], PATHINFO_EXTENSION);
        $imgName = "$imgKey.$imgExt";
        $imgPath = "$folder/$imgName";

        move_uploaded_file($imgTmpPath, $imgPath);

        $templateProcessor->setImageValue($imgKey, [
            'path' => $imgPath,
            'width' => 200,
            'height' => 200,
            'ratio' => true
        ]);
    } else {
        // تعويض فارغ في حال عدم رفع الصورة
        $templateProcessor->setValue($imgKey, '');
    }
}


// حفظ الملف النهائي
$templateProcessor->saveAs($savePath);

// عرض رابط التحميل
echo "<p>تم إنشاء التحضير بنجاح!</p>";
echo "<a href='$savePath' download>تحميل التحضير</a>";
