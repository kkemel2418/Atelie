<?php

class CampanhaController {
    public function index() {
        include_once 'Database.php';
    
        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();
    
        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de seleção
                $stmt = $conn->prepare("SELECT * FROM campanhas");
    
                // Execute a declaração
                $stmt->execute();
    
                // Obtenha os resultados como um array associativo
                $campanhas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                // Se houver resultados
                if ($campanhas) {
                    // Retorne os resultados como JSON
                    header('Content-Type: application/json');
                    echo json_encode($campanhas);
                } else {
                    // Se não houver resultados, retorne uma mensagem adequada
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(array('message' => 'Nenhuma campanha encontrada.'));
                }
    
            } catch (PDOException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados.'));
        }
    }
    
    
    public function show($id) {
        include_once 'Database.php';
    
        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();
    
        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de seleção
                $stmt = $conn->prepare("SELECT * FROM empresas WHERE id = :id");
    
                // Bind o valor do ID
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
                // Execute a declaração
                $stmt->execute();
    
                // Obtenha os resultados como um array associativo
                $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
                // Se a empresa for encontrada
                if ($empresa) {
                    // Retorne a empresa como JSON
                    header('Content-Type: application/json');
                    echo json_encode($empresa);
                } else {
                    // Se a empresa não for encontrada, retorne uma mensagem adequada
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(array('message' => 'Empresa não encontrada.'));
                }
    
            } catch (PDOException $e) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados.'));
        }
    }
    

    public function create() {
        // Receber os dados da campanha do corpo da requisição (JSON)
        $requestData = json_decode(file_get_contents('php://input'), true);
    
        // Verificar se os dados foram recebidos corretamente
        if ($requestData && !empty($requestData['titulo']) && !empty($requestData['descricao'])
                         && !empty($requestData['data_inicio']) && !empty($requestData['data_termino'])
                         && !empty($requestData['empresa_id'])) {
    
            include_once 'Database.php';
    
            // Crie uma nova instância da classe Database
            $database = new Database();
            $conn = $database->getConnection();
    
            // Verifique se a conexão foi bem-sucedida
            if ($conn) {
                try {
                    // Verificar se a campanha já existe
                    if ($this->checkIfCampanhaExists($requestData['titulo'])) {
                        header('HTTP/1.1 409 Conflict');
                        echo json_encode(array('message' => 'Já existe uma campanha com esse título'));
                        return; // Retorna para evitar a inserção
                    }
    
                    // Prepare a declaração de inserção
                    $stmt = $conn->prepare("INSERT INTO campanhas (titulo, descricao, data_inicio, data_termino, empresa_id) 
                                           VALUES (:titulo, :descricao, :data_inicio, :data_termino, :empresa_id )");
    
                    // Bind os valores
                    $stmt->bindParam(':titulo', $requestData['titulo']);
                    $stmt->bindParam(':descricao', $requestData['descricao']);
                    $stmt->bindParam(':data_inicio', $requestData['data_inicio']);
                    $stmt->bindParam(':data_termino', $requestData['data_termino']);
                    $stmt->bindParam(':empresa_id', $requestData['empresa_id']);
    
                    // Execute a declaração
                    if ($stmt->execute()) {
                        // Campanha cadastrada com sucesso
                        echo json_encode(array('message' => 'Campanha cadastrada com sucesso'));
                    } else {
                        // Erro ao cadastrar a campanha
                        echo json_encode(array('message' => 'Erro ao cadastrar a campanha'));
                    }
                } catch (PDOException $e) {
                    // Erro ao cadastrar a campanha
                    echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
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
    

    public function checkIfCampanhaExists($titulo) {
        include_once 'Database.php';
    
        $database = new Database();
        $conn = $database->getConnection();
    
        if ($conn) {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM campanhas WHERE titulo = :titulo");
                $stmt->bindParam(':titulo', $titulo);
                $stmt->execute();
    
                $result = $stmt->fetchColumn();
    
                return $result > 0; // Se o resultado for maior que zero, significa que o título já existe
            } catch (PDOException $e) {
                echo json_encode(array('message' => 'Erro: Ja existe uma campanha com essse nome' . $e->getMessage()));
                return false;
            }
        } else {
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
            return false;
        }
    }

    public function update($id, $requestData) {
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
                    if ($field !== 'id') {
                        $updateFields .= "$field = :$field, ";
                    }
                }
    
                // Remove a vírgula extra no final da string
                $updateFields = rtrim($updateFields, ', ');
    
                // Prepare a declaração de atualização
                $stmt = $conn->prepare("UPDATE campanhas SET $updateFields WHERE id = :id");
    
                // Bind os valores
                foreach ($requestData as $field => $value) {
                    $stmt->bindParam(":$field", $value);
                }
                $stmt->bindParam(':id', $id);
    
                // Execute a declaração
                if ($stmt->execute()) {
                    // Campanha atualizada com sucesso
                    echo json_encode(array('message' => 'Campanha atualizada com sucesso !'));
                } else {
                    // Erro ao atualizar a campanha
                    echo json_encode(array('message' => 'Erro ao atualizar a campanha !'));
                }
            } catch (PDOException $e) {
                // Erro ao atualizar a campanha
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            // Erro na conexão com o banco de dados
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
        }
    }
    

    public function delete($id) {
        include_once 'Database.php';
     
        // Crie uma nova instância da classe Database
        $database = new Database();
        $conn = $database->getConnection();
    
        // Verifique se a conexão foi bem-sucedida
        if ($conn) {
            try {
                // Prepare a declaração de exclusão
                $stmt = $conn->prepare("DELETE FROM campanhas WHERE id = :id");
    
                // Bind o valor do ID
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
                // Execute a declaração
                if ($stmt->execute()) {
                    // Campanha excluída com sucesso
                    header('Content-Type: application/json');
                    echo json_encode(array('message' => 'Campanha excluída com sucesso'));
                } else {
                    // Erro ao excluir a campanha
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode(array('message' => 'Erro ao excluir a campanha'));
                }
            } catch (PDOException $e) {
                // Erro ao excluir a campanha
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(array('message' => 'Erro: ' . $e->getMessage()));
            }
        } else {
            // Erro na conexão com o banco de dados
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('message' => 'Erro na conexão com o banco de dados'));
        }
    }
    
    
    
    

}

