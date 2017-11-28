# s3-upload-php
Repositório para realizar upload e multipart upload de arquivos para o S3

A idéia deste código é que o sistema decida quando utilizar <strong>Multipart Upload</strong>, 
ou quando realizar o <strong>Upload</strong> diretamente ao bucket pelo putObject.

Aqui foi definido que todo arquivo maior que <strong>5MB</strong> deve ser feito o Multipart Upload.

Como único pré-requisito você deverá editar o arquivo config.php
E adicionar as suas <strong>ACCESS_KEY</strong>, <strong>SECRET_ACCESS_KEY</strong> e <strong>Bucket</strong> ou você pode configurar uma role para isso, para
que consiga inicializar uma instância do S3 e utilizar os recursos contidos.
