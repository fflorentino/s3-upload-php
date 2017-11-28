<?php
	
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\s3\MultipartUpload;
use Aws\S3\MultipartUploadException;

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

    //Check the file size to decide whether 
    //to use common upload or multipart-upload 
    if($_FILES['file']['size'] > 5000000){
    $filesize = '0';
    }else{
        $filesize = '1';
    }

    echo $filesize;

    if($filesize > 0){
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
        //End Common Upload

    }else{
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

                //Put Object on S3 Bucket
                try {
                 //Create a new multipart upload and get the id to use in future
                   $multipart = $s3->createMultipartUpload(array(
                        'Bucket'       => $config['s3']['bucket'],
                        'Key'          => "uploads/{$name}",
                        'StorageClass' => 'REDUCED_REDUNDANCY',
                        'ACL'          => 'public-read',
                        'Metadata'     => array(
                            'param1' => 'value 1',
                            'param2' => 'value 2',
                            'param3' => 'value 3'
                        )
                    ));

                    //Guardo o UploadID 
                    $multipartId = $multipart['UploadId'];

                    //Upload the file in parts.   
                    $file = fopen($tmp_file_path, 'r');
                    $parts = array();
                    $partNumber = 1;
                    while (!feof($file)) {
                        $multipart = $s3->uploadPart(array(
                            'Bucket'     => $config['s3']['bucket'],
                            'Key'        => "uploads/{$name}",
                            'UploadId'   => $multipartId,
                            'PartNumber' => $partNumber,
                            'Body'       => fread($file, 5 * 1024 * 1024),
                        ));
                        $parts[] = array(
                            'PartNumber' => $partNumber++,
                            'ETag'       => $multipart['ETag'],
                        );

                        echo "Uploading part {$partNumber} of {$name}.\n";
                    }
                    fclose($file);
                } catch (S3Exception $e) {
                        $multipart = $s3->abortMultipartUpload(array(
                            'Bucket'   => $config['s3']['bucket'],
                            'Key'      => "uploads/{$name}",
                            'UploadId' => $multipartId,
                        ));

                        echo "Upload of {$name} failed.\n";
                    }

                //Complete multipart upload.
                echo $partNumber;
                $multipart = $s3->completeMultipartUpload(array(
                    'Bucket'   => $config['s3']['bucket'],
                    'Key'      => "uploads/{$name}",
                    'UploadId' => $multipartId,
                    'MultipartUpload' => Array(
                            'Parts' => $parts,
                    ),
                ));
                $url = $multipart['Location'];

                echo "Uploaded {$name} to {$url}.\n";

                //Remove the temp file
                unlink($tmp_file_path);
            } 
        }
?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="UTF-8">
        <title>Upload</title>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.3/css/mdb.min.css">
    </head>
    <body>
        <div>
            <form class="form-data" action="index.php" method="post" enctype="multipart/form-data">
                <input type="file" name="file">
                <input type="submit" value="Upload">
            </form>
        </div>    
        <h2>I'm Alive!</h2>                            
    </body>
</html>
