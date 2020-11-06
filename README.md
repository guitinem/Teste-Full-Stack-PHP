# Teste-Full-Stack-PHP
- Criação de uma api rest utilizando apenas PHP (sem nenhum uso de framework)
- Cliente para consumir os dados da api e mostrar na tela, podendo realizar operações de CRUD


## Requisitos

- Php 7.0^
- [Composer](https://getcomposer.org/download/)
- Mysql
- (opcional, facilita na execução) [xampp](https://www.apachefriends.org/pt_br/index.html)

## Instalação

> Os comandos utilizados neste documento são baseados na distribuição Linux Ubuntu 20.04

Vamos começar por instalar as dependências do projeto, utilize o comando abaixo

```shell
composer install
```


## Preparando o ambiente

Mude o nome do arquivo **.env.example** para **.env**, e coloque as credenciais de acesso ao banco mysql:

```shell
# Exemplo:
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=api_oncar
DB_USERNAME=root
DB_PASSWORD=root
```


Depois de realizado a conexão, crie a seguinte tabela no banco, (crie a tabela no database fornecido nas váriaveis de ambiente):

```sql
CREATE TABLE car (
    id INT NOT NULL AUTO_INCREMENT,
    veiculo VARCHAR(100) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    ano INT DEFAULT NULL,
    descricao VARCHAR(360) DEFAULT 'nenhuma descrição',
  	vendido tinyint(1) default 0,
  	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
  	updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (id)
)

```


## Testando o projeto

> O ambiente foi testado utilizando o xampp, no caso, o projetou rodou dentro da pasta htdocs

execute o comando a abaixo para iniciar a api:
> php -S 127.0.0.1:8001 -t public

Após a confirmação que a api está rodando, acesse o cliente entrando pelo navegador:

http://localhost:8001/site/client.html

### Atenção


Caso você queira rodar a api em outra porta, não tem problema, apenas não esqueça de alterar o valor no script do site para apontar o endereço correto da api:


No arquivo:

**public/site/client.html**

altere o valor da variável **api** na linha *120*




