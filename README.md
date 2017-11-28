# s3-upload-php
Repositório para realizar upload e multipart upload de arquivos para o S3

A idéia deste código é que o sistema decida quando utilizar <strong>Multipart Upload</strong>, 
ou quando realizar o <strong>Upload</strong> diretamente ao bucket pelo putObject.

Aqui foi definido que todo arquivo maior que <strong>5MB</strong> deve ser feito o Multipart Upload

Você deverá <strong>criar uma pasta</strong> chamada <i>"files"</i> <strong>no mesmo nível do diretório</strong> <i>"app"</i> para que seja gerado um arquivo
temporario antes do envio ao S3, o mesmo é escluído após o processo

Como pré-requisito você deverá editar o arquivo config.php
E adicionar as suas <strong>ACCESS_KEY</strong>, <strong>SECRET_ACCESS_KEY</strong> e <strong>BUCKET</strong> ou você pode configurar uma role para isso, para
que consiga inicializar uma instância do S3 e utilizar os recursos contidos.

Após é necessário efetuar a instalação do SDK do PHP com o composer.
Como o arquivo <strong>composer.json</strong> já esta criado e com o require para a SDK basta utilizar o composer para instalar.

$ composer install 
