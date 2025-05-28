<?php

namespace App\Console\Commands;

use App\Http\Controllers\FoodsDefinitionController;
use Exception;
use Illuminate\Console\Command;


class FoodsDefinitionInteraction extends Command
{
    // Nome e descrição do comando Artisan
    protected $signature = 'food:interactive';
    protected $description = 'Welcome! Interaja com a API para a sugestão de trocas de alimentos
    em uma refeição...\n\n';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            do {
                echo "1 - Alterar/Substituir 1 alimento em uma refeição\n";
                echo "0 - Sair\n";
        
                $choice = $this->ask('Digite a opção desejada: ');
        
                if (!is_numeric($choice) || !in_array(intval($choice), [1, 0])) {
                    echo "Opção inválida. Por favor, tente novamente.\n";
                    continue;
                }
        
                if ($choice == 1) {
                    try{
                        do
                    {
                    $food = $this->ask('Que alimento você deseja substituir?\nDigite o nome do alimento:\t');
                        if(!$food)
                        {
                            $this->error('Você deve digitar um alimento (ex: Frango, Cuscuz...)');
                        }
                    }
                    while(!($food));
                    }catch(Exception $e){
                        echo "Ocorreu um erro: " . $e->getMessage() . "\n";
                    }
            
                    $food = trim($food); // Remove espaços extras no início e no final
                    $food = ucfirst(strtolower($food)); // Converte para minúsculas e coloca a primeira letra maiúscula
                    try{
                    if(!FoodsDefinitionController::validateArgFood($food)){
                        
                        do{
                            echo("O alimento '$food' não é válido ou não foi encontrado. Por favor,
                            digite novamente: exemplo(Comida)-Nessa formatação");
                            $food = $this->ask('Que alimento você deseja substituir?\nDigite o nome do alimento:\t');
                        }while(FoodsDefinitionController::validateArgFood($food));
                        
                    }
                }catch(Exception $e){
                    echo "Ocorreu um erro: " . $e->getMessage() . "\n";
                }

                try{
                    $meal = $this->ask('Para que possamos sugerir uma substituição
                    adequada, por favor, informe todos os componentes da refeição em que ele
                    está (exemplo: Arroz, Frango, Alface , Tomate).\n\nDigite os componentes da sua refeição:\t');
                    
                    while(!($meal)){
                        $this->error('Você precisa digitar os componentes da refeição.\n');
                        $meal = $this->ask('Digite os componentes da sua refeição: (exemplo: Arroz, Frango,
                        Alface, Tomate)\t');
                    }
                    }catch(Exception $e){
                        echo "Ocorreu um erro: " . $e->getMessage() . "\n";
                    }
            
            
                    $isFine = FoodsDefinitionController::validateMeal($meal, $food);
                try{
                    while(!$isFine){
                        $this->error('Sua refeição precisa conter o alimento que deseja ser alterado');
                        $meal = $this->ask('Digite novamente os componentes da sua refeição com o ALIMENTO QUE DESEJA
                        SER ALTERADO: (exemplo: Arroz, Frango, Alface, Tomate. Onde Arroz é quem será alterado)\t');
                        $isFine = FoodsDefinitionController::validateMeal($meal, $food);
                    }
                }catch(Exception $e){
                    echo "Ocorreu um erro: " . $e->getMessage() . "\n";
                }
            
                $foodObject = FoodsDefinitionController::returnFoodUser($food);

                    echo('Que bom, tudo de acordo! Só um instante...\n');
                    echo('Buscando substituto adequado...\n');
                    echo('Alteração bem-sucedida! Verifique agora como ficou sua refeição com a melhor substituição encontrad:');
            
                    if (is_string($meal)) {
                        $meal = explode(", ", $meal); // converte a string em array de alimentos
                        $meal = array_map('trim', $meal); // remove espaços extras ao redor de cada alimento
                    } else {
                        // Caso a refeição já seja um array, não há necessidade de usar explode
                        $meal = array_map('trim', $meal); // apenas faz a limpeza dos espaços se for um array
                    }

                    $meal = FoodsDefinitionController::replaceFood($meal, $foodObject, $food);
            
                    echo "\n+------------------------+\n";
                    echo "|        Refeição        |\n";
                    echo "+------------------------+\n";
            
                    foreach ($meal as $index) {
                        printf("| %-23s |\n", $index);  // %-22s: Alinha a string à esquerda e reserva 22 espaços
                    }
            
                    echo "+------------------------+\n";
                    continue;
                    echo ('Saindo...\n');
                } elseif ($choice == 0) {
                    echo ('Saindo...\n');
                    break;
                }
        
            } while (true);
        } catch (Exception $e) {
            echo "Ocorreu um erro: " . $e->getMessage() . "\n";
        }        
    }
}
