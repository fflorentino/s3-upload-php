<?php
	
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

	require '../vendor/autoload.php';

	$config = require('config.php');

	//Create S3 Instance

	try{
		$s3 = S3Client::factory(
			array(
				'credentials' => array(
					'key' => $config['s3']['key'],
					'secret' => $config['s3']['secret']
				),
				'version' => 'latest',
				'region'  => 'sa-east-1'
			)
		);

	}catch (Exception $e) {
			die("Error: " . $e->getMessage());
	}

	if(isset($_FILES['file'])){
 
    $file = $_FILES['file'];
 
    // File details
    $name = $file['name'];
    $tmp_name = $file['tmp_name'];
 
    $extension = explode('.', $name);
    $extension = strtolower(end($extension));

    $key = md5(uniqid());
    $tmp_file_name = "{$key}.{$extension}";
    $tmp_file_path = "../files/{$tmp_file_name}";
 
    // Move the file
    move_uploaded_file($tmp_name, $tmp_file_path);
    var_dump($tmp_file_path);

    //Put Object on S3 Bucket
    try {

    	$s3->putObject([
    		'Bucket' => $config['s3']['bucket'],
    		'Key' => "uploads/{$name}",
    		'Body' => fopen($tmp_file_path, 'rb'),
    		'ACL' => 'public-read'
    	]);

    	//Remove the temp file
    	unlink($tmp_file_path);
    	
    } catch (S3Exception $e) {
		die("Ocorreu um erro ao realizar o upload do arquivo");
    }

	}


?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="UTF-8">
        <title>Upload</title>
    </head>
    <body>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" value="Upload">
        </form>
        <h2>I'm Alive!</h2>
    </body>
</html>
