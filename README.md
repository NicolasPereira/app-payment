# App Payments

Esta aplicação tem como objetivo realizar a transação de transferência de dinheiro entre dois usuários.

## Tecnologias

- Docker
- PHP
- Laravel
- MySQL

## User Story

- Deve existir dois tipos de usuários, normais e lojistas. É necessário que os usuários possuam Nome Completo, CPF, e-mail
e senha. CPF/CNPJ e e-mails devem ser unicos no sistema. Deste modo, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou
e-mail.

- Usuários normais podem enviar e receber dinheiro para lojistas e outros usuários.

- Lojistas só recebem dinheiro, não podem realizar envio de dinheiro par ninguém.

- Validar se o usuário tem saldo antes da transferência

- Consultar serviço autorizador externo para validar a transferência.

- A operação de transferência deve ser uma transação, ou seja, pode ser revertida em caso de inconsistência e o dinheiro deve voltar
para a carteira do usuário que envia.

- No recebimento do pagamento o usuário que recebeu dinheiro deve receber uma notificação enviada por um serviço de terceiro,
que pode estar disponível/indisponível.

- Este serviço deve ser RESTFull

## Como rodar este projeto

### Clone este repositório
```bash
    git clone https://github.com/NicolasPereira/app-payment.git
```

### Utilizando docker, crie um volume para o banco de dados
```bash
    docker create volume mysqldb
```

### Execute o docker para buildar o ambiente
```
    docker-compose build
```

### Instale os pacotes necessários
```bash
    docker-compose exec web composer install
```

### Gere o arquivo .env

```bash
    docker-compose exec web cp .env.example .env
```

### Crie a chave da aplicação
```bash
    docker-compose exec web php artisan key:generate
```

### Execute as migrations
```bash
    docker-compose exec web php artisan migrate
```

## Aplicação

Pare realizar a transação é necessário realizar uma requisição `POST` para `api/transaction`

### Headers
Essa aplicação aceita somente requisições do tipo `application/json`

### Payload de envio

Para realizar a requisição é necessário informar este payload:
```json
{
    "payer" : 4,
    "payee" : 15,
    "value" : 100.00
}
```

Sendo:

- `payer` é o ID do usuário que está realizando a transação caso o usuário não exista na base de dados ira retornar `HTTP CODE 422`
- `payer` é o ID do usuário que está recebendo a transação caso o usuário não exista na base de dados ira retornar `HTTP CODE 422`
- `value` é o valor da transação, caso seja menor ou igual a zero retornar `HTTP CODE 422`

### Payload de resposta

Ao executar uma transaction com sucesso será retornado os segeuintes dados:
```json
{
    "transaction": {
        "id": "0a0d8828-ade5-4c1e-9e04-905b6ef4058d",
        "value": "100.00",
        "created_at": "2021-11-16T06:38:22.000000Z",
        "payer": {
            "user": {
                "name": "Caleb Krajcik II",
                "email": "wunsch.daniella@example.net",
                "type": "client"
            },
            "balance": "99.99"
        },
        "payee": {
            "name": "Mr. Dorcas Kunze",
            "email": "eorn@example.net",
            "type": "client"
        }
    }
}
```

Sendo:
- `id` é o ID da transação.
- `value` o valor enviado na transação.
- `created_at` é data de criação da transação.

Payer:
- `user`
  - `name` é o nome do usuário que pagou
  - `email` é o email do usuário que pagou
  - `type` é o tipo de perfil, podendo ser para o `payer` apenas `client`
  - `balance` é o valor atualizado na carteira do usuário que enviou o pagamento.

Payee:
- `name` é o nome do usuário que pagou
- `email` é o email do usuário que pagou
- `type` é o tipo de perfil, podendo ser para o payee `client` ou `shopkeeper`

## Erros
Caso retorne algum erro, este será o payload.
```json
{
    "errors": {
        "message": "error message"
    }
}
```

# Testes
Realizei um total de 8 testes validando o `TransactionController`, com esses testes consegui validar os erros que a
API gera conforme cada contexto esperado.

Para executar os testes desse projeto é necessário executar
```bash
    docker-compose exec web php artisan test
```

# Autor
Nicolas Pereira </br>
[LinkedIn](https://www.linkedin.com/in/nicolas-pereira/) [Twitter](https://twitter.com/devnic_)
