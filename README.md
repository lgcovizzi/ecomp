### Cards Hierárquicos
```
Instituição
├── Endereço 1
│   ├── Departamento 1
│   └── Departamento 2
├── Endereço 2
│   └── Departamento 3
└── ...
```

### Acesso aos Serviços
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000
- **Banco de Dados**: localhost:3306

# Limpar e reconstruir tudo
docker-compose down -v && docker-compose up --build -d
```

## 📊 API Endpoints

### Instituições
```
GET    /api/instituicoes
POST   /api/instituicoes
PUT    /api/instituicoes/{id}
DELETE /api/instituicoes/{id}
```

### Endereços
```
GET    /api/enderecos
GET    /api/enderecos/instituicao/{id}
POST   /api/enderecos
PUT    /api/enderecos/{id}
DELETE /api/enderecos/{id}
```

### Departamentos
```
GET    /api/departamentos
GET    /api/departamentos/instituicao/{id}
GET    /api/departamentos/endereco/{id}
POST   /api/departamentos
PUT    /api/departamentos/{id}
DELETE /api/departamentos/{id}
```

### Registros 
```
GET    /api/registros
POST   /api/registros
PUT    /api/registros/{id}
DELETE /api/registros/{id}
```

## 🏗️ Estrutura de Diretórios

```
sistema_instituição/
├── backend/                    # Laravel 11 Backend
│   ├── app/Models/            # Eloquent Models
│   ├── app/Http/Controllers/ # API Controllers
│   ├── routes/api.php         # Rotas da API
│   ├── database/migrations/   # Migrations
│   └── .env.example          # Configuração
├── frontend/                   # Vue.js 3 Frontend
│   ├── src/views/            # Páginas principais
│   ├── src/components/       # Componentes Vue
│   ├── src/stores/          # Pinia stores
│   ├── src/services/        # Serviços de API
│   └── vite.config.ts       # Configuração Vite
├── docker-compose.yml        # Orquestração Docker
└── README.md               # Este arquivo
```
