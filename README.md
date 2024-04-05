<img src="gifhead.gif">

Este projeto consiste na implementação de uma API simples para gerenciamento de contas bancárias, sem a necessidade de persistência em banco de dados. O objetivo é fornecer dois endpoints básicos: GET /balance e POST /event, para consultar o saldo de uma conta e realizar eventos como depósitos, retiradas e transferências.

### 🔧 Instalação

Para instalar e executar este projeto localmente, siga estas etapas:

1. Clone o repositório:

```
git clone https://github.com/RDudaRocha/ebanx_challenge.git
```

2. Navegue até o diretório do projeto:

```
cd seu-repositorio
```

3. Instale as dependências:
Certifique-se de ter o Composer instalado. Se não tiver, você pode baixá-lo em getcomposer.org.
```
composer install
```

4. Configure o servidor web:
Você pode usar o servidor web embutido do PHP ou configurar um servidor como Apache ou Nginx. Por exemplo, para iniciar o servidor embutido do PHP:
```
php -S localhost:8000
```

5. Acesse o projeto no navegador:
Abra seu navegador e navegue até http://localhost:8000.

## ⚙️ Executando os testes

Aqui está um guia passo-a-passo para executar os testes automatizados para este sistema:

Redefinir o estado antes de iniciar os testes:

Certifique-se de que o sistema esteja em um estado inicial consistente antes de iniciar os testes.

```
POST /reset
```
Resposta Esperada:

```
200 OK
```

Obter saldo de uma conta inexistente:

Execute um teste para verificar o saldo de uma conta que não existe.
```
GET /balance?account_id=1234
```
Resposta Esperada:
```
404 0
```
Criar uma conta com saldo inicial:

Crie uma conta com um saldo inicial especificado.
```
POST /event {"type":"deposit", "destination":"100", "amount":10}
```
Resposta Esperada:
```
201 {"destination": {"id":"100", "balance":10}}
```
Depositar em uma conta existente:

Faça um depósito em uma conta que já existe.
```
POST /event {"type":"deposit", "destination":"100", "amount":10}
```
Resposta Esperada:
```
201 {"destination": {"id":"100", "balance":20}}
```
Obter saldo de uma conta existente:

Verifique o saldo de uma conta que já existe.
```
GET /balance?account_id=100
```
Resposta Esperada:
```
200 20
```
Sacar de uma conta inexistente:

Tente fazer um saque de uma conta que não existe.
```
POST /event {"type":"withdraw", "origin":"200", "amount":10}
```
Resposta Esperada:
```
404 0
```
Sacar de uma conta existente:

Faça um saque de uma conta que já existe.
```
POST /event {"type":"withdraw", "origin":"100", "amount":5}
```
Resposta Esperada:
```
201 {"origin": {"id":"100", "balance":15}}
```
Transferir de uma conta existente:

Realize uma transferência de uma conta existente para outra.
```
POST /event {"type":"transfer", "origin":"100", "amount":15, "destination":"300"}
```
Resposta Esperada:
```
201 {"origin": {"id":"100", "balance":0}, "destination": {"id":"300", "balance":15}}
```
Transferir de uma conta inexistente:

Tente fazer uma transferência de uma conta que não existe.
```
POST /event {"type":"transfer", "origin":"200", "amount":15, "destination":"300"}
```
Resposta Esperada:
```
404 0
```

## 🛠️ Construído com

* [PhP](https://www.php.net/) - Linguagem usada

---
<div align="center">⌨️ com ❤️ por [Duda Rocha](https://rdudarocha.github.io/portifolio/) 😊</div>
