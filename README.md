
# Laravel Horizon

Como configurar o Horizon para utilizar em produção no Laravel 10



## Instalação

Dentro da pasta do projeto digite os comandos abaixo:

```bash
  composer install
  npm install
```
    
## Requisitos
- Laravel 10
- Laravel horizon
- Redis 
- Supervisor

## Configurando autenticação

No arquivo .env alterar o `APP_ENV=local`  para `APP_ENV=production`

Adicionar as variaveis de ambientes para a autenticação:

`HORIZON_BASIC_AUTH_USERNAME='adicionar_usuário_acesso'`, adiconar o usuário que sera utilizado para autenticação no login

`HORIZON_BASIC_AUTH_PASSWORD='adicionar_senha_acesso'`, adiconar a senha que sera utilizada para autenticação no login

Para que o horizon funcione perfeitamente iremos utilizar o redis como database.

Altere a `QUEUE_CONNECTION` de sync para redis.

Adicione `REDIS_CLIENT=predis` abaixo de `REDIS_PORT`

Abra o arquivo `HorizonServiceProvider` que esta na pasta:

📂 `app/Providers/HorizonServiceProvider.php`

Comentar  a função `gate` e adiconar a funcão authorization

```php
protected function authorization()
    {
        Horizon::auth(function ($request){
            return true;
        });
    }
```
Criar um middleware especifico para a autenticação do horizon, exemplo:

```shell
php artisan make:middleware HorizonBasicAuthMiddleware
```
Será criado um middleware com o nome de HorizonBasicAuthMiddleware, localizado em:

📂 `app/Http/Middleware/HorizonBasicAuthMiddleware.php`

Implementar o metodo hadle dentro do middleware `HorizonBasicAuthMiddleware.php`:

```php
public function handle(Request $request, Closure $next): Response
    {
        $authenticationHasPassed = false;
        if (!App::environment('local')){

            $authenticationHasPassed = false;

            if ($request->header('PHP_AUTH_USER',null) && $request->header('PHP_AUTH_PW', null)){
                $username = $request->header('PHP_AUTH_USER');
                $password = $request->header('PHP_AUTH_PW');

                if (
                    $username === config('horizon.basic_auth.username')
                    && $password === config('horizon.basic_auth.password')
                ){
                    $authenticationHasPassed = true;
                }

            }
            if ($authenticationHasPassed === false){
                return response()->make('Credenciais Inválidas.', 401, ['WWW-Authenticate' => 'Basic']);
            }

        }
        return $next($request);
    }
```

Proximo passo é alterar as informações do arquivo horizon.php, que esta na pasta:

📂 `config/horizon.php`

Adicone o array `basic_auth`, junto com seus valores dentro do return:
```php
return [

    'basic_auth' =>[
       'username' => env('HORIZON_BASIC_AUTH_USERNAME', 'horizon'),
        'password' => env('HORIZON_BASIC_AUTH_PASSWORD', 'password')
    ],
//...continuação do codigo
```
Ainda no mesmo arquivo adicionar alias para o middleware que acabou de criar, neste caso aqui foi adiconado o alias `horizonBasicAuth`, que ficará assim:
```php
'middleware' => ['web','horizonBasicAuth'],
```

Agora vamos adiconar o nosso middleware no kernel da aplicação, o arquico se encontra em:

📂 `app/Http/Kernel.php`

Desça ate encontrar `protected $middlewareAliases` , dentro do array adicione o middleware que você criou

```php
'horizonBasicAuth'=> \App\Http\Middleware\HorizonBasicAuthMiddleware::class,
//...continuação do codigo
```
Perceba que o chave que se refere ao middleware `HorizonBasicAuthMiddleware` é o mesmo alias que foi adicionado no arquivo `config/horizon.php`

Fazendo todo este passo a passo corretamente sua aplicação com horizon já esta pronta para ser utilizada em produção, exigindo a autenticação para acessar o painel do horizon.

## Configurando um banco de dados redis exclusivo para o horizon

Vá para o `config/database.php`, e adicione dentro do array do redis esta configuração, alterando apenas o valor do `REDIS_DB`, para um valor de 0 a 15, e que naõ está em uso 

```PHP
'horizon' => [
			'prefix' => env(
				'HORIZON_PREFIX',
				Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
			),
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '5'), // alterar este valor
        ],
```
Vá novamente ao arquivo `config/horizon.php`,e procure por `Horizon Redis Connection`

logo abaixo altere o `'use' => 'default'`, para `'use' => 'horizon',`

## Configurando Supervisor

Dentro de `/etc/supervisor/conf.d` crie um arquivo de configuração para o supervisor, neste caso criaremos um arquivo
laravel-horizon.conf.

vamos adicionar o codigo para que o supervisor possa iniciar e monitorar os processos do horizon:

```
[program:laravel-horizon]
process_name=%(program_name)s
command=php /home/forge/example.com/artisan horizon 
autostart=true
autorestart=true
user=forge
redirect_stderr=true
stdout_logfile=/home/forge/example.com/horizon.log
stopwaitsecs=3600
```
Altere o valor do `command` para o path do seu artisan da aplicação

Altere o valor do `user` para o o usuario do sistema

Altere o `stdout_logfile` para p path que deseja salvar o arquivo de log
## Aprendizados

O que você aprendeu construindo esse projeto? Quais desafios você enfrentou e como você superou-os?


## Referência

 - [Dev Tech Tips Brasil - Horizon em produção](https://www.youtube.com/watch?v=ZBGGhHD-1pU)
 - [Dev Tech Tips Brasil - Otimização do horizon](https://www.youtube.com/watch?v=LpHaRYKwvlI)

