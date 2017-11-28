# s3-upload-php
Repositório para realizar upload e multipart upload de arquivos para o S3

A idéia deste código é que o sistema decida quando utilizar Multipart Upload, 
ou quando realizar o upload diretamente ao bucket pelo putObject.

Aqui foi definido que todo arquivo maior que 5MB deve ser feito o Multipart Upload.
