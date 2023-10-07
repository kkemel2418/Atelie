<?php

class EmpresaController {

    public function index() {

        include_once 'Database.php';
    
        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();
    
        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de seleção
                $stmt = $conn->prepare("SELECT * FROM empresas");
    
                // Execute a declaração
                $stmt->execute();
    
                // Obtenha os resultados como um array associativo
                $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                // Se houver empresas encontradas
                if ($empresas) {
                    // Retorne as empresas como JSON
                    header('Content-Type: application/json');
                    echo json_encode($empresas);
                } else {
                    // Se não houver empresas, retorne uma mensagem adequada
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(array('message' => 'Nenhuma empresa encontrada.'));
                }
    
            } catch (PDOException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('message' => 'Erro: Empresa com o mesmo nome já cadastrado! ') );
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados.'));
        }
    }
    

  
    public function show($id) {
        include_once 'Database.php';
    
        $database = new Database();
        $conn = $database->getConnection();
    
        if ($conn) {
            try {
    
                $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
    
                $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($empresa) {
                    // Retorna os dados como JSON
                    header('Content-Type: application/json');
                    echo json_encode($empresa);
                } else {
                    echo json_encode(array("mensagem" => "Empresa não encontrada."));
                }
    
            } catch (PDOException $e) {
                echo json_encode(array("mensagem" => "Erro: " . $e->getMessage()));
            }
        } else {
            echo json_encode(array("mensagem" => "Erro na conexão com o banco de dados."));
        }
    }

    public function create() {
        // Receber os dados da empresa do corpo da requisição (JSON)
        $requestData = json_decode(file_get_contents('php://input'), true);

        
        // Verificar se os dados foram recebidos corretamente
        if ($requestData && !empty($requestData['cnpj']) && !empty($requestData['razao_social'])
                         && !empty($requestData['endereco']) && !empty($requestData['responsavel']) && 
                          !empty($requestData['email']) && !empty($requestData['telefone'])) {
    
            include_once 'Database.php';

            // Crie uma nova instância da classe Database
            $database = new Database();
            $conn = $database->getConnection();
    
            // Verifique se a conexão foi bem-sucedida
            if ($conn) {
                try {
                    // Prepare a declaração de inserção
                    $stmt = $conn->prepare("INSERT INTO empresas (cnpj, razao_social, endereco, responsavel, email, telefone) 
                                           VALUES (:cnpj, :razao_social, :endereco, :responsavel, :email, :telefone )");
    
                    // Bind os valores
                    $stmt->bindParam(':cnpj', $requestData['cnpj']);
                    $stmt->bindParam(':razao_social', $requestData['razao_social']);
                    $stmt->bindParam(':endereco', $requestData['endereco']);
                    $stmt->bindParam(':responsavel', $requestData['responsavel']);
                    $stmt->bindParam(':email', $requestData['email']);
                    $stmt->bindParam(':telefone', $requestData['telefone']);
    
                    // Obtém a data e hora atual
                    $dataAtual = date('Y-m-d H:i:s');      
    
                    // Execute a declaração
                    if ($stmt->execute()) {
                        // Empresa cadastrada com sucesso
                        echo json_encode(array('message' => 'Empresa cadastrada com sucesso'));
                    } else {
                        // Erro ao cadastrar a empresa
                        echo json_encode(array('message' => 'Erro ao cadastrar a empresa'));
                    }
                } catch (PDOException $e) {
                    // Erro ao cadastrar a empresa
                   echo json_encode(array('message' => 'Erro:  CNPJ já cadastrado ! ')); // cnpj duplicado 
            
                }
            } else {
                // Erro na conexão com o banco de dados
                echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
            }
    
        } else {
            // Dados inválidos ou faltando
            echo json_encode(array('message' => 'Verifique se os dados estão completos'));
        }
    }
    

    public function update($cnpj, $requestData) {
        include_once 'Database.php';
    
        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();
    
        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                $updateFields = '';
    
                // Monta a string de atualização
                foreach ($requestData as $field => $value) {
                    if ($field !== 'cnpj') {
                        $updateFields .= "$field = :$field, ";
                    }
                }
    
                // Remove a vírgula extra no final da string
                $updateFields = rtrim($updateFields, ', ');
    
                // Prepare a declaração de atualização
                $stmt = $conn->prepare("UPDATE empresas SET $updateFields WHERE cnpj = :cnpj");
    
                // Bind os valores
                foreach ($requestData as $field => $value) {
                    $stmt->bindParam(":$field", $value);
                }
                $stmt->bindParam(':cnpj', $cnpj);
    
                // Execute a declaração
                if ($stmt->execute()) {
                    // Empresa atualizada com sucesso
                    echo json_encode(array('message' => 'Empresa atualizada com sucesso !'));
                } else {
                    // Erro ao atualizar a empresa
                    echo json_encode(array('message' => 'Erro ao atualizar a empresa !'));
                }
            } catch (PDOException $e) {
                // Erro ao atualizar a empresa
                echo json_encode(array('message' => 'Erro: Verifique se não está passando algum valor vazio ou chave com nome errada ' . $e->getMessage()));
            }
        } else {
            // Erro na conexão com o banco de dados
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
        }
    }
    
    public function delete($cnpj) {
        
        include_once 'Database.php';

  
        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();

    
        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de exclusão
                $stmt = $conn->prepare("DELETE FROM empresas WHERE cnpj = :cnpj");
    
                // Bind o valor do CNPJ
                $stmt->bindParam(':cnpj', $cnpj);
    
                // Execute a declaração
                if ($stmt->execute()) {
                    // Empresa excluída com sucesso
                    echo json_encode(array('message' => 'Empresa excluída com sucesso'));
                } else {
                    // Erro ao excluir a empresa
                    echo json_encode(array('message' => 'Erro ao excluir a empresa'));
                }
            } catch (PDOException $e) {
                // Erro ao excluir a empresa
                echo json_encode(array('message' => 'Erro:???? ' . $e->getMessage()));
            }
        } else {
            // Erro na conexão com o banco de dados
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
        }
    }
    
}
