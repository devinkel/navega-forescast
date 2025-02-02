<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class TestRunner extends Command
{
    // O nome do comando que será chamado no Artisan
    protected $signature = 'test:run {file?}';

    // A descrição do comando
    protected $description = 'Rodar os testes usando PHPUnit, com filtro de arquivo';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtém o argumento de arquivo, caso seja passado
        $file = $this->argument('file');

        // Comando básico para rodar o PHPUnit
        $command = ['vendor/bin/phpunit', '--testdox', '--colors=always','--stop-on-failure'];

        // Se um arquivo foi passado, adiciona o filtro
        if ($file) {
            $command[] = $file;
        }

        // Cria e executa o processo do comando
        $process = new Process($command);
        $process->run();

        // // Verifica se o comando foi bem sucedido
        // if (!$process->isSuccessful()) {
        //     $this->error('Erro ao rodar os testes: ' . $process->getErrorOutput(). $file);
        //     return 1;
        // }

        // Caso contrário, exibe a saída dos testes
        $this->info($process->getOutput());

        return 0;
    }
}