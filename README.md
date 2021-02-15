# Poke Trader

Permite que o usuário monte uma troca contendo de 1 a 6 pokemons e calula se a troca é justa ou não. Em caso positivo, permite realizar a troca.
Também mantém o histórico das trocas permitindo ver quando a troca foi fechada e quais os pokemons trocados.

Para respeitar o tempo proposto no desafio foi utilizado PHP e também não foi gasto tempo com usabilidade e melhorias nas telas, assim como poderia ter sido feito uso de JS para preencher os dados sem recarregar as páginas ou até mesmo o Front-end desacopado usando API.

Outra funcionalidade importante que não foi desenvolvida a fim de respeitar o tempo proposto foi um login onde o usuário pudesse criar a troca e outro usuário aceitá-la.


## Arquivos implementados

Os principais arquivos implementados, que não são o do framework, foram:

- [src/Controller/TradeController.php](src/Controller/TradeController.php): Controller com as rotas para listar e montar as trocas;
- [src/Entity/Trade.php](src/Entity/Trade.php): Entidade Trade para mapear os campos do banco de dados e regras específicas da entidade;
- [src/Repository/TradeRepository.php](src/Repository/TradeRepository.php): Repositório para buscar as trocas (Trade);
- [src/Service/PokemonService.php](src/Service/PokemonService.php): Classe com algumas regras para lidar com o objeto Pokemon;
- [src/Service/TraderService.php](src/Service/TraderService.php): Classe com algumas regras para lidar com o objeto Trade, principalmente o cálculo se a troca é justa;
- [templates/trade/index.html.twig](templates/trade/index.html.twig): Front para a tela de listagem de trocas (Home);
- [templates/trade/show.html.twig](templates/trade/show.html.twig): Front para a tela de detalhe e montagem da troca;
- [tests/](tests/): Testes unitários para as classes de serviços.