# Artisan Surf Forecast

[Read this in English](#artisan-surf-forecast-english-version)

Artisan Surf Forecast é um aplicativo que adiciona um comando Artisan ao Lumen para gerar uma tabela com a previsão de ondas diárias num intervalo de 3 horas.

A ideia surgiu porque trabalho em um ambiente onde há muita observação presencial, e como um apaixonado por surf, verifico a previsão de ondas várias vezes ao dia. Para facilitar essa visualização sem precisar abrir um navegador, criei um comando Artisan.

Já precisava mesmo aprofundar o conhecimento em comandos para automatizar tarefas aqui na empresa. Uni o útil ao agradável.

Todos são bem-vindos a contribuir, bastando fazer um fork do repositório e criar um pull request com suas contribuições.

## Descrição

Há um arquivo com todas as cidades litorâneas brasileiras em `config/coastal_cities_in_brazil.php`.

O comando busca a latitude e longitude de uma cidade com base em sugestões, utilizando uma API. Em seguida, consulta outra API para obter a previsão de ondas, normaliza os dados e retorna uma tabela organizada.

O projeto foi desenvolvido para explorar recursos customizados do Laravel/Lumen de forma didática e divertida.

## Tecnologias Utilizadas

- **Lumen**
- Se quiser explorar mais recursos além do comando principal, veja abaixo:
    - **PostgreSQL**
    - **Docker e Docker Compose** (com Nginx, PHP, PostgreSQL e Adminer)
    - **Migrations e Seeds**
    - **Testes automatizados**

## Instalação e Execução

### Com ambiente local:
```sh
composer install
php artisan forecast:run
```

### Usando Docker:
```sh
docker-compose up -d
php artisan forecast:run
```

### Com recursos adicionais:
```sh
composer install
php artisan run:migrate
php artisan db:seed
php artisan test:run
```

## Recursos
- Consulta automática de coordenadas geográficas
- Busca e normalização da previsão de ondas
- Armazenamento no banco de dados (opcional)
- Rotas para gerar previsões (opcional)
- Testes para garantir a qualidade do código (opcional)

## Exemplo de Uso
![Selecting a state](/public/example/state.png)
![Selecting a city](/public/example/city.png)
![Result](/public/example/table.png)

## Licença
Este projeto é de uso livre.

---

# Artisan Surf Forecast (English Version)

Artisan Surf Forecast is an app that adds an Artisan command to Lumen to generate a table with daily wave forecasts at 3-hour intervals.

The idea came from working in an environment where many people look over my shoulder (in person). As a surf enthusiast, I often check wave forecasts multiple times a day. So, I decided to create an Artisan command to make viewing forecasts easier without opening a browser.

I also needed to deepen my knowledge of command-line automation in my company, so I combined learning with fun.

Everyone is welcome to contribute to the project, as long as they fork the repository and create a pull request with their contributions.

## Description

There is a file containing all Brazilian coastal cities at `config/coastal_cities_in_brazil.php`.

The command fetches a city's latitude and longitude based on suggestions using an API. Then, it retrieves the wave forecast from another API, normalizes the data, and returns a well-structured table.

This project was developed to explore Laravel/Lumen's custom features in an educational and enjoyable way.

## Technologies Used

- **Lumen**
- If you want to explore additional features beyond the `php artisan forecast:run` command:
    - **PostgreSQL**
    - **Docker and Docker Compose** (with Nginx, PHP, PostgreSQL, and Adminer)
    - **Migrations and Seeds**
    - **Automated tests**

## Installation and Execution

### Using local environment:
```sh
composer install
php artisan forecast:run
```

### Using Docker:
```sh
docker-compose up -d
php artisan forecast:run
```

### With additional features:
```sh
composer install
php artisan run:migrate
php artisan db:seed
php artisan test:run
```

## Features
- Automatic geographic coordinates lookup
- Fetch and normalize wave forecasts
- Store data in a database (optional)
- Routes to generate forecasts (optional)
- Tests to ensure code quality (optional)

## Example Usage
![Selecting a state](/public/example/state.png)
![Selecting a city](/public/example/city.png)
![Result](/public/example/table.png)

## License
This project is free to use.
