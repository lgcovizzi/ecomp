## Configuração do Banco de Dados

### Ambiente Docker
- **Serviço**: `mariadb_db`
- **Imagem**: `mariadb:10.6`
- **Porta**: `3306` (mapeada para localhost:3306)
- **Variáveis de Ambiente**:
  - `MYSQL_ROOT_PASSWORD=rootpassword`
  - `MYSQL_DATABASE=sistema_instituicao`
  - `MYSQL_USER=sistema_user`
  - `MYSQL_PASSWORD=sistema_password`

### Conexão Local
```bash
# Via Docker
mysql -h localhost -P 3306 -u sistema_user -p sistema_instituicao

# Via container
docker-compose exec mariadb_db mysql -u sistema_user -p sistema_instituicao
```

## Diagrama Entidade-Relacionamento Completo

```
┌─────────────────────────┐    ┌─────────────────────────┐    ┌─────────────────────────┐    ┌─────────────────────────┐
│      instituições       │    │       endereços         │    │     departamentos       │    │       registros         │
├─────────────────────────┤    ├─────────────────────────┤    ├─────────────────────────┤    ├─────────────────────────┤
│ id: BIGINT PK AI      │1  N│ id: BIGINT PK AI      │1  N│ id: BIGINT PK AI      │1  N│ id: BIGINT PK AI      │
│ nome_longo: VARCHAR   │────│ titulo: VARCHAR       │────│ nome: VARCHAR         │────│ nome: VARCHAR         │
│ nome_curto: VARCHAR   │    │ cidade: VARCHAR       │    │ instituicao_id: FK    │    │ instituicao_id: FK    │
│ created_at: TIMESTAMP │    │ estado: VARCHAR       │    │ endereco_id: FK       │    │ endereco_id: FK       │
│ updated_at: TIMESTAMP │    │ instituicao_id: FK    │    │ created_at: TIMESTAMP │    │ departamento_id: FK   │
│                       │    │ created_at: TIMESTAMP │    │ updated_at: TIMESTAMP │    │ created_at: TIMESTAMP │
│                       │    │ updated_at: TIMESTAMP │    │                       │    │ updated_at: TIMESTAMP │
└─────────────────────────┘    └─────────────────────────┘    └─────────────────────────┘    └─────────────────────────┘

Relacionamentos:
- instituicoes.enderecos: CASCADE DELETE
- instituicoes.departamentos: CASCADE DELETE
- instituicoes.registros: SET NULL
- enderecos.departamentos: CASCADE DELETE
- enderecos.registros: SET NULL
- departamentos.registros: SET NULL
```

## Estrutura Detalhada das Tabelas

### 1. Tabela `instituicoes`
| Coluna        | Tipo          | Atributos                  | Descrição                                   |
|---------------|---------------|----------------------------|---------------------------------------------|
| `id`          | BIGINT(20)    | PRIMARY KEY AUTO_INCREMENT | Identificador único da instituição          |
| `nome_longo`  | VARCHAR(255)  | NOT NULL UNIQUE            | Nome completo da instituição                |
| `nome_curto`  | VARCHAR(255)  | NOT NULL UNIQUE            | Nome curto/abreviado                        |
| `created_at`  | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora de criação                      |
| `updated_at`  | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora da última atualização           |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE KEY `instituicoes_nome_longo_unique` (`nome_longo`)
- UNIQUE KEY `instituicoes_nome_curto_unique` (`nome_curto`)

**Model Laravel:** `App\Models\Instituicao`
- **Relationships:**
  - `hasMany` → `Endereco`
  - `hasMany` → `Departamento`
  - `hasMany` → `Registro`

### 2. Tabela `enderecos`
| Coluna          | Tipo          | Atributos                  | Descrição                                   |
|-----------------|---------------|----------------------------|---------------------------------------------|
| `id`            | BIGINT(20)    | PRIMARY KEY AUTO_INCREMENT | Identificador único do endereço             |
| `titulo`        | VARCHAR(255)  | NOT NULL                   | Título/nome do endereço                     |
| `cidade`        | VARCHAR(255)  | NOT NULL                   | Cidade do endereço                          |
| `estado`        | VARCHAR(255)  | NOT NULL                   | Estado do endereço                        |
| `instituicao_id`| BIGINT(20)    | FOREIGN KEY                | Referência para instituição                 |
| `created_at`    | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora de criação                      |
| `updated_at`    | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora da última atualização           |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE KEY `enderecos_titulo_instituicao_id_unique` (`titulo`, `instituicao_id`)
- FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE
- INDEX `enderecos_instituicao_id_index` (`instituicao_id`)

**Model Laravel:** `App\Models\Endereco`
- **Relationships:**
  - `belongsTo` → `Instituicao`
  - `hasMany` → `Departamento`
  - `hasMany` → `Registro`

### 3. Tabela `departamentos`
| Coluna          | Tipo          | Atributos                  | Descrição                                   |
|-----------------|---------------|----------------------------|---------------------------------------------|
| `id`            | BIGINT(20)    | PRIMARY KEY AUTO_INCREMENT | Identificador único do departamento         |
| `nome`          | VARCHAR(255)  | NOT NULL                   | Nome do departamento                        |
| `instituicao_id`| BIGINT(20)    | FOREIGN KEY                | Referência para instituição                 |
| `endereco_id`   | BIGINT(20)    | FOREIGN KEY                | Referência para endereço                    |
| `created_at`    | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora de criação                      |
| `updated_at`    | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora da última atualização           |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE KEY `departamentos_nome_endereco_id_unique` (`nome`, `endereco_id`)
- FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE CASCADE
- FOREIGN KEY (`endereco_id`) REFERENCES `enderecos` (`id`) ON DELETE CASCADE
- INDEX `departamentos_instituicao_id_index` (`instituicao_id`)
- INDEX `departamentos_endereco_id_index` (`endereco_id`)

**Model Laravel:** `App\Models\Departamento`
- **Relationships:**
  - `belongsTo` → `Instituicao`
  - `belongsTo` → `Endereco`
  - `hasMany` → `Registro`

### 4. Tabela `registros`
| Coluna            | Tipo          | Atributos                  | Descrição                                   |
|-------------------|---------------|----------------------------|---------------------------------------------|
| `id`              | BIGINT(20)    | PRIMARY KEY AUTO_INCREMENT | Identificador único do registro             |
| `nome`            | VARCHAR(255)  | NOT NULL                   | Nome do funcionário/registro                |
| `instituicao_id`  | BIGINT(20)    | FOREIGN KEY NULLABLE       | Referência opcional para instituição        |
| `endereco_id`     | BIGINT(20)    | FOREIGN KEY NULLABLE       | Referência opcional para endereço           |
| `departamento_id` | BIGINT(20)    | FOREIGN KEY NULLABLE       | Referência opcional para departamento       |
| `created_at`      | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora de criação                        |
| `updated_at`      | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP  | Data e hora da última atualização           |

**Índices:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes` (`id`) ON DELETE SET NULL
- FOREIGN KEY (`endereco_id`) REFERENCES `enderecos` (`id`) ON DELETE SET NULL
- FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL
- INDEX `registros_instituicao_id_index` (`instituicao_id`)
- INDEX `registros_endereco_id_index` (`endereco_id`)
- INDEX `registros_departamento_id_index` (`departamento_id`)

**Model Laravel:** `App\Models\Registro`
- **Relationships:**
  - `belongsTo` → `Instituicao`
  - `belongsTo` → `Endereco`
  - `belongsTo` → `Departamento`

## Mapeamento Completo API Endpoints

### Instituições (`/api/instituicoes`)
- **GET** `/api/instituicoes` - Listar todas instituições com contador de registros
- **POST** `/api/instituicoes` - Criar nova instituição
  ```json
  {
    "nome_longo": "Universidade Federal de São Paulo",
    "nome_curto": "UNIFESP"
  }
  ```
- **PUT** `/api/instituicoes/{id}` - Atualizar instituição
- **DELETE** `/api/instituicoes/{id}` - Deletar instituição (CASCADE para endereços e departamentos)

### Endereços (`/api/enderecos`)
- **GET** `/api/enderecos` - Listar todos endereços
- **GET** `/api/enderecos/instituicao/{id}` - Listar endereços por instituição
- **POST** `/api/enderecos` - Criar novo endereço
  ```json
  {
    "titulo": "Campus São Paulo",
    "cidade": "São Paulo",
    "estado": "SP",
    "instituicao_id": 1
  }
  ```
- **PUT** `/api/enderecos/{id}` - Atualizar endereço
- **DELETE** `/api/enderecos/{id}` - Deletar endereço (CASCADE para departamentos)

### Departamentos (`/api/departamentos`)
- **GET** `/api/departamentos` - Listar todos departamentos
- **GET** `/api/departamentos/instituicao/{id}` - Listar departamentos por instituição
- **GET** `/api/departamentos/endereco/{id}` - Listar departamentos por endereço
- **POST** `/api/departamentos` - Criar novo departamento
  ```json
  {
    "nome": "Departamento de Informática",
    "instituicao_id": 1,
    "endereco_id": 1
  }
  ```
- **PUT** `/api/departamentos/{id}` - Atualizar departamento
- **DELETE** `/api/departamentos/{id}` - Deletar departamento (SET NULL em registros)

### Registros (`/api/registros`)
- **GET** `/api/registros` - Listar todos registros com relacionamentos completos
- **POST** `/api/registros` - Criar novo registro
  ```json
  {
    "nome": "João Silva",
    "instituicao_id": 1,
    "endereco_id": 1,
    "departamento_id": 1
  }
  ```
- **PUT** `/api/registros/{id}` - Atualizar registro
- **DELETE** `/api/registros/{id}` - Deletar registro

## Relacionamentos e Integridade Referencial

### Resumo de Relações
| Relação | Tipo | Direção | Ação DELETE | Descrição |
|---------|------|---------|-------------|-----------|
| instituicoes.enderecos | 1:N | → | CASCADE | Instituição → Múltiplos endereços |
| instituicoes.departamentos | 1:N | → | CASCADE | Instituição → Múltiplos departamentos |
| instituicoes.registros | 1:N | → | SET NULL | Instituição → Múltiplos registros |
| enderecos.departamentos | 1:N | → | CASCADE | Endereço → Múltiplos departamentos |
| enderecos.registros | 1:N | → | SET NULL | Endereço → Múltiplos registros |
| departamentos.registros | 1:N | → | SET NULL | Departamento → Múltiplos registros |

### Regras de Negócio Implementadas

#### Unicidade de Dados
1. **Instituição**: `nome_longo` e `nome_curto` únicos no sistema
2. **Endereço**: `titulo` único por instituição (composto: `titulo` + `instituicao_id`)
3. **Departamento**: `nome` único por endereço (composto: `nome` + `endereco_id`)

#### Integridade Referencial
- **Validação em Cascata**: Seleção dinâmica no frontend garante consistência
- **Campos Opcionais**: Registros podem existir sem vínculos (NULL permitido)
- **Deleção Segura**: ON DELETE CASCADE/SET NULL previne registros órfãos

## SQL Avançado - Consultas Operacionais

### Dashboard Analytics
```sql
-- Visão geral do sistema
SELECT 
    'instituicoes' as entidade, COUNT(*) as total FROM instituicoes
UNION ALL
SELECT 'enderecos', COUNT(*) FROM enderecos
UNION ALL  
SELECT 'departamentos', COUNT(*) FROM departamentos
UNION ALL
SELECT 'registros', COUNT(*) FROM registros;

-- Instituições com estrutura completa
SELECT 
    i.nome_longo as instituicao,
    COUNT(DISTINCT e.id) as total_enderecos,
    COUNT(DISTINCT d.id) as total_departamentos,
    COUNT(DISTINCT r.id) as total_registros
FROM instituicoes i
LEFT JOIN enderecos e ON i.id = e.instituicao_id
LEFT JOIN departamentos d ON i.id = d.instituicao_id
LEFT JOIN registros r ON i.id = r.instituicao_id
GROUP BY i.id, i.nome_longo
ORDER BY total_registros DESC;

-- Estrutura hierárquica completa
SELECT 
    i.nome_longo as instituicao,
    e.titulo as endereco,
    e.cidade,
    e.estado,
    d.nome as departamento,
    GROUP_CONCAT(r.nome ORDER BY r.nome) as funcionarios
FROM instituicoes i
LEFT JOIN enderecos e ON i.id = e.instituicao_id
LEFT JOIN departamentos d ON e.id = d.endereco_id
LEFT JOIN registros r ON d.id = r.departamento_id
GROUP BY i.id, e.id, d.id
ORDER BY i.nome_longo, e.titulo, d.nome;
```

### Manutenção e Auditoria
```sql
-- Verificar integridade dos dados
SELECT 
    'Registros sem instituição' as problema,
    COUNT(*) as quantidade
FROM registros 
WHERE instituicao_id IS NOT NULL 
  AND instituicao_id NOT IN (SELECT id FROM instituicoes)
UNION ALL
SELECT 'Registros sem endereço',
    COUNT(*) 
FROM registros 
WHERE endereco_id IS NOT NULL 
  AND endereco_id NOT IN (SELECT id FROM enderecos)
UNION ALL
SELECT 'Registros sem departamento',
    COUNT(*) 
FROM registros 
WHERE departamento_id IS NOT NULL 
  AND departamento_id NOT IN (SELECT id FROM departamentos);

-- Backup estrutural
CREATE TABLE IF NOT EXISTS backup_estrutura AS
SELECT 
    i.nome_longo, i.nome_curto,
    e.titulo as endereco_titulo, e.cidade, e.estado,
    d.nome as departamento_nome,
    r.nome as funcionario_nome,
    r.created_at as registro_data
FROM registros r
LEFT JOIN instituicoes i ON r.instituicao_id = i.id
LEFT JOIN enderecos e ON r.endereco_id = e.id  
LEFT JOIN departamentos d ON r.departamento_id = d.id;
```

## Laravel Models & Eloquent Relationships

### Model: Instituicao (`app/Models/Instituicao.php`)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instituicao extends Model
{
    protected $fillable = ['nome_longo', 'nome_curto'];
    
    public function enderecos()
    {
        return $this->hasMany(Endereco::class);
    }
    
    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }
    
    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}
```

### Model: Endereco (`app/Models/Endereco.php`)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $fillable = ['titulo', 'cidade', 'estado', 'instituicao_id'];
    
    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }
    
    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }
    
    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}
```

### Model: Departamento (`app/Models/Departamento.php`)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $fillable = ['nome', 'instituicao_id', 'endereco_id'];
    
    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }
    
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }
    
    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}
```

### Model: Registro (`app/Models/Registro.php`)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $fillable = ['nome', 'instituicao_id', 'endereco_id', 'departamento_id'];
    
    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }
    
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }
    
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
```

## Laravel Migrations - Ordem de Execução

### 1. Migration: `2024_01_01_000001_create_instituicoes_table.php`
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instituicoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome_longo')->unique();
            $table->string('nome_curto')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instituicoes');
    }
};
```

### 2. Migration: `2024_01_01_000002_create_enderecos_table.php`
```php
Schema::create('enderecos', function (Blueprint $table) {
    $table->id();
    $table->string('titulo');
    $table->string('cidade');
    $table->string('estado');
    $table->foreignId('instituicao_id')->constrained('instituicoes')->onDelete('cascade');
    $table->unique(['titulo', 'instituicao_id']);
    $table->timestamps();
});
```

### 3. Migration: `2024_01_01_000003_create_departamentos_table.php`
```php
Schema::create('departamentos', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->foreignId('instituicao_id')->constrained('instituicoes')->onDelete('cascade');
    $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('cascade');
    $table->unique(['nome', 'endereco_id']);
    $table->timestamps();
});
```

### 4. Migration: `2024_01_01_000004_create_registros_table.php`
```php
Schema::create('registros', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->foreignId('instituicao_id')->nullable()->constrained('instituicoes')->onDelete('set null');
    $table->foreignId('endereco_id')->nullable()->constrained('enderecos')->onDelete('set null');
    $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->onDelete('set null');
    $table->timestamps();
});
```

## Docker Database Operations

### Comandos Essenciais
```bash
# Acessar banco de dados via container
docker-compose exec mariadb_db mysql -u sistema_user -p sistema_instituicao

# Executar arquivo SQL
docker-compose exec mariadb_db mysql -u sistema_user -p sistema_instituicao < backup.sql

# Dump completo do banco
docker-compose exec mariadb_db mysqldump -u sistema_user -p sistema_instituicao > backup_completo.sql

# Resetar banco (limpar tudo)
docker-compose exec backend php artisan migrate:fresh --seed

# Verificar logs do banco
docker-compose logs mariadb_db
```

### Variáveis de Ambiente (.env)
```bash
# Backend Laravel
DB_CONNECTION=mysql
DB_HOST=mariadb_db
DB_PORT=3306
DB_DATABASE=sistema_instituicao
DB_USERNAME=sistema_user
DB_PASSWORD=sistema_password

# Docker Compose
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=sistema_instituicao
MYSQL_USER=sistema_user
MYSQL_PASSWORD=sistema_password
```

## Performance & Otimização

### Índices Recomendados
```sql
-- Índices adicionais para performance
CREATE INDEX idx_registros_nome ON registros(nome);
CREATE INDEX idx_enderecos_cidade_estado ON enderecos(cidade, estado);
CREATE INDEX idx_instituicoes_nome_combined ON instituicoes(nome_longo, nome_curto);
```

### Configuração MariaDB (docker-compose.yml)
```yaml
mariadb_db:
  image: mariadb:10.6
  environment:
    - MYSQL_ROOT_PASSWORD=rootpassword
    - MYSQL_DATABASE=sistema_instituicao
    - MYSQL_USER=sistema_user
    - MYSQL_PASSWORD=sistema_password
  ports:
    - "3306:3306"
  volumes:
    - mariadb_data:/var/lib/mysql
  command: --default-authentication-plugin=mysql_native_password
```