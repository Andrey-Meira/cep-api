
# API de Busca de Endereço por CEP

Esta API permite consultar um endereço com base no CEP informado, utilizando a API externa [ViaCEP](https://viacep.com.br/ws/). A API implementa cache com Redis para melhorar a performance e evitar consultas repetidas.

## Funcionalidades

- **Busca de Endereço**: Ao informar um CEP, a API retorna as informações de endereço correspondentes, como logradouro, bairro, cidade e estado.
- **Validação de CEP**: O CEP informado é validado antes da consulta para garantir que tenha exatamente 8 dígitos numéricos.
- **Cache com Redis**: A resposta da API é armazenada em cache por 24 horas para melhorar a performance e evitar chamadas repetidas para a API externa.

## Dependências

A API é construída com o Symfony e utiliza as seguintes dependências:

- **PHP 8.3.14**
- **Symfony 5.10.4**
- **Redis**: Necessário para o cache de dados.
- **HttpClientInterface**: Para realizar as requisições HTTP à API externa.

## Instalação

1. **Clone o repositório**

   Abra o terminal e execute o seguinte comando para clonar o repositório:

   ```bash
   git clone https://github.com/Andrey-Meira/cep-api.git
   ```

2. **Instalar as dependências**

   Navegue até o diretório do projeto e execute o comando abaixo para instalar as dependências:

   ```bash
   composer install
   ```

3. **Rodando o servidor local**

   Para rodar o servidor de desenvolvimento do Symfony, execute o seguinte comando:

   ```bash
   composer start
   ```

## Endpoints

### `GET /cep/{cep}`

Retorna as informações de endereço correspondentes ao CEP informado.

**Parâmetros:**

- `cep` (string): O CEP a ser consultado. O CEP deve ser válido e ter 8 dígitos numéricos.

**Exemplo de resposta:**

```json
{
    "cep": "01001-000",
    "logradouro": "Praça da Sé",
    "bairro": "Sé",
    "cidade": "São Paulo",
    "estado": "SP"
}
```

**Código de resposta:**

- `200 OK`: Se o CEP for válido e os dados forem encontrados.
- `400 Bad Request`: Se o CEP informado for inválido.
- `404 Not Found`: Se o CEP não for encontrado na API externa.

## Testando Localmente

Para testar a API localmente, você pode usar ferramentas como [Postman](https://www.postman.com/) ou [cURL](https://curl.se/) para enviar requisições HTTP.

Exemplo de requisição cURL:

```bash
curl -X GET "http://localhost:8080/cep/01001000"
```

## Autor

Este projeto foi desenvolvido por [Andrey Meria](https://github.com/Andrey-Meira).
