<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件上传</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">选择文件：</label> <span>data.csv</span>
        <input type="file" name="file" id="file" required>
        <br>
        <label for="key">输入口令：</label>
        <input type="password" name="key" id="key" required>
        <br>
        <input type="submit" value="上传">
    </form>

    <?php
    // 设置口令
    $secretKey = 'sss';

    // 检查口令是否匹配
    if ($_POST['key'] == $secretKey) {
        // 处理文件上传
        if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $uploadPath = './';  // 上传文件存储路径
            $filename = $_FILES['file']['name'];

            // 如果文件已存在，则删除
            if (file_exists($uploadPath . $filename)) {
                unlink($uploadPath . $filename);
            }

            // 移动上传的文件到指定路径
            move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath . $filename);

            echo '文件上传成功！';

            // 读取并处理数据
            $dataFile = $uploadPath . 'data.csv';
            $emailFile = $uploadPath . 'email.txt';

            if (file_exists($dataFile)) {
                $emailArray = [];

                $handle = fopen($dataFile, 'r');
                fgetcsv($handle);				 //跳过第一行！
                while (($data = fgetcsv($handle)) !== false) {
                    $emailArray[] = $data[0];
                }

                fclose($handle);

                // 将邮箱写入email.txt
                file_put_contents($emailFile, implode("\n", $emailArray));

                echo '邮箱提取成功并保存到email.txt！';

                // 删除data.csv文件
                unlink($dataFile);
            } else {
                die('找不到data.csv文件。');
            }
        } else {
            die('文件上传失败。');
        }
    } else {
        // 如果口令不匹配，可以在这里添加其他逻辑
    }
    ?>
</body>
</html>
