Atelie Campanha

Uma API rest baseado em ações, promovendo a administração para o responsável pela campanha vigente.

A API oferece os seguintes endpoints principais:

    Empresas:
        GET /api/empresas: Retorna a lista de empresas registradas.
        GET /api/empresas/{id}: Retorna os detalhes da empresa com o ID especificado.
        POST /api/empresas: Cria uma nova empresa.
        PUT /api/empresas/  : Atualiza os detalhes de uma empresa existente.
        DELETE /api/empresas/{id}: Exclui uma empresa existente.

    Campanhas:
        GET /api/campanhas: Retorna a lista de campanhas disponíveis.
        GET /api/campanhas/{id}: Retorna os detalhes da campanha com o ID especificado.
        POST /api/campanhas: Cria uma nova campanha.
        PUT /api/campanhas/{id}: Atualiza os detalhes de uma campanha existente.
        DELETE /api/campanhas/{id}: Exclui uma campanha existente.

    Participantes:
        GET /api/participantes: Retorna a lista de participantes registrados.
        GET /api/participantes/{id}: Retorna os detalhes do participante com o ID especificado.
        POST /api/participantes: Cria um novo participante.
        PUT /api/participantes/{id}: Atualiza os detalhes de um participante existente.
        DELETE /api/participantes/{id}: Exclui um participante existente.

        

# Considerações Importantes !
     Não foi feito validaçao do CNPJ nesse cadastro(Algoritmo).Apenas nao permite que seja duplicado.

Como Funciona

Cada entidade tem um CRUD individual.
Não é possivel cadastrar em duplicidade nenhum nos item. O participante não podem ser cadastrados duas vezes na mesma campanha.


git clone [https://github.com/seu-usuario/seu-projeto.git](https://github.com/kkemel2418/Atelie/tree/master)
cd atelie


Link do PostMan:
https://kaliamin.postman.co/workspace/New-Team-Workspace~df28cbb4-d847-4de8-82fb-813b32d39803/collection/14051827-38452e4e-fbd5-49ee-a353-7cb05854899b?action=share&creator=14051827

