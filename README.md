
![1](https://img.shields.io/badge/%5E7.29-Laravel-orange?style=flat-square&logo=laravel)
![2](https://img.shields.io/badge/Licence-MIT-yellow?style=flat-square)
![3](https://img.shields.io/badge/1.59.0-Visual%20Studio%20Code-purple?style=flat-square&logo=visual-studio-code)
![4](https://img.shields.io/badge/7.4.19-PHP-informational?style=flat-square&logo=php)
![5](https://img.shields.io/badge/5.7.24-MySQL-lightblue?style=flat-square&logo=mysql)

## Documentação

Documentação [aqui](https://github.com/ramonlima08/teste_simplificado/wiki)

## Projeto

Desenvolver uma API para transferencia de valores entre usuários da plataforma.

# Funcionalidades da API

- Criação de Usuários (usuário ou lojista)
- Login com autenticação JWT
- Exibição dos dados do Usuário Logado
- Lista de Permissões do Usuário
- Histórico de Transações monetárias
- Transferência de valor monetário
- Desfazer Transferências
- Inclusão de Saldo na Carteira (para iniciar os testes)
- Verificação do Saldo atual
- Envio de notificação (sms ou e-mail)

## Instalação 

Baiar o projeto

Instala o composer
```bash
$ composer install
```

Instala o npm
```bash
$ npm install
```

Copia arquivo .env.example para arquivo .env
```bash
$ cp .env.example .env
```

Gera chave
```bash
$ php artisan key:generate
```

Publicar o JWT
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

Gerar a chave JWT
```bash
php artisan jwt:secret
```

Cria banco de dados vazio
```bash
$ mysql -uroot -proot
$ create database avaliacao;
$ quit;
```

Configurar banco de dados no arquivo .env
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=avaliacao
DB_USERNAME=root
DB_PASSWORD=root
```

Migra tabelas para o banco de dados
```bash
$ php artisan migrate
```

Envia as seeds para o banco de dados
```bash
$ php artisan db:seed
```

Roda servidor local na porta 8000 (caso não esteja rodando laragon)
```bash
$ php artisan serve
```
## Usabilidade 

Não foi desenvolvido a interface visual, para testes recomenda-se utilizar o programa Postman https://www.postman.com/