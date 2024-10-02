<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sistema de Criação de Provas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-center">
          <h3>Sistema de Criação de Provas</h3>
        </div>
        <div class="card-body">
          <form id="provaForm">

            <div class="mb-3">
              <label for="headerImage" class="form-label">Cabeçalho (Imagem):</label>
              <input type="file" class="form-control" id="headerImage" name="headerImage" accept="image/*">
            </div>
            
            <div class="mb-3">
              <label for="materia" class="form-label">Nome da Matéria:</label>
              <select class="form-select" id="materia" name="materia" required>
                <option value="Matemática">Matemática</option>
                <option value="História">História</option>
                <option value="Geografia">Geografia</option>
                <option value="Biologia">Biologia</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="dificuldade" class="form-label">Nível de Dificuldade:</label>
              <select class="form-select" id="dificuldade" name="dificuldade" required>
                <option value="Fácil">Fácil</option>
                <option value="Médio">Médio</option>
                <option value="Difícil">Difícil</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="conteudo" class="form-label">Conteúdo:</label>
              <input type="text" class="form-control" id="conteudo" name="conteudo" placeholder="Digite o conteúdo" required>
            </div>

            <div class="mb-3">
              <label for="detalhe" class="form-label">Detalhe (opcional):</label>
              <input type="text" class="form-control" id="detalhe" name="detalhe" placeholder="Detalhe adicional">
            </div>

            <div class="mb-3">
              <label for="quantidade" class="form-label">Número de Perguntas:</label>
              <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" max="20" placeholder="Número de perguntas" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Gerar Prova</button>
          </form>
        </div>
      </div>

      <!-- Exibição das Mensagens -->
      <div class="card mt-4">
        <div class="card-header text-center">
          <h5>Resultado</h5>
        </div>
        <div class="card-body">
          <div class="messages">
            <p>Aqui será exibido o resultado da prova gerada.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  $("#provaForm").submit(function (event) {
      event.preventDefault();
  
      // Captura os valores do formulário
      const materia = $("#materia").val();
      const dificuldade = $("#dificuldade").val();
      const conteudo = $("#conteudo").val();
      const detalhe = $("#detalhe").val();
      const quantidade = $("#quantidade").val();
  
      // Monta o prompt com base nas escolhas do usuário
      let prompt = `Crie ${quantidade} questões fechadas de ${materia} de nível ${dificuldade} sobre ${conteudo}.`;
      if (detalhe.trim() !== '') {
        prompt += ` Detalhes adicionais: ${detalhe}.`;
      }
  
      // Faz o POST via Ajax
      $.ajax({
        url: "/chat",
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': "{{csrf_token()}}"
        },
        data: {
          "content": prompt
        },
        xhrFields: {
          responseType: 'blob' // Importante para garantir o download de arquivo binário
        },
        success: function(res) {
            // Cria uma URL para o blob recebido e força o download
            const url = window.URL.createObjectURL(new Blob([res]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'questoes-geradas.docx'); // Nome do arquivo
            document.body.appendChild(link);
            link.click();
            link.remove(); // Remove o link após o clique
        },
        error: function() {
            alert('Erro ao gerar o arquivo.');
        }
      });
  });
  </script>
  
</body>
</html>
