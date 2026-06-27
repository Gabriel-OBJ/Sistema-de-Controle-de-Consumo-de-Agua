# 💧 Sistema de Controle de Consumo de Água

> Sistema web desenvolvido em **Laravel 13** para gestão do abastecimento de água de associações comunitárias. Permite o registro mensal de leituras de medidores, cálculo automático do consumo e geração de faturas, com notificação via WhatsApp.

---

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Tecnologias](#tecnologias)
- [Funcionalidades](#funcionalidades)
- [Regras de Negócio](#regras-de-negócio)
- [Requisitos](#requisitos)
- [Instalação e Configuração](#instalação-e-configuração)
- [Estrutura do Banco de Dados](#estrutura-do-banco-de-dados)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Uso do Sistema](#uso-do-sistema)
- [Commits](#commits)

---

## Sobre o Projeto

Uma associação comunitária gerencia o abastecimento de água de seus membros. Todo mês, um leiturista registra o consumo (em m³) de cada medidor. Este sistema automatiza esse processo, calculando o valor da fatura com base em regras de cobrança configuráveis e permitindo notificar cada consumidor via WhatsApp com os detalhes do consumo e o valor a pagar.

---

## Tecnologias

| Camada | Tecnologia |
|--------|-----------|
| **Backend** | PHP 8.3 + Laravel 13 |
| **Banco de Dados** | MySQL 8+ |
| **ORM** | Eloquent (Laravel) |
| **Frontend** | Blade Templates |
| **CSS Framework** | Bootstrap 5.3 (via CDN) |
| **Ícones** | Bootstrap Icons 1.11 (via CDN) |
| **Fontes** | Google Fonts — Inter |
| **Gerenciador de Deps.** | Composer 2 |

---

## Funcionalidades

- ✅ **Cadastro de Consumidores** — Listar, cadastrar e editar consumidores com número de medidor único
- ✅ **Registro de Leitura** — Formulário mensal com preenchimento automático da leitura anterior
- ✅ **Validações de Negócio** — Leitura atual ≥ anterior | Uma leitura por consumidor/mês
- ✅ **Cálculo Automático** — Consumo em m³ calculado e fatura gerada instantaneamente
- ✅ **Gestão de Faturas** — Listagem filtrada por mês/ano com totais de pendente e pago
- ✅ **Marcar como Pago** — Atualização de status com confirmação
- ✅ **Configuração de Taxa** — Gestor pode alterar taxa fixa e valor excedente
- ✅ **Bônus WhatsApp** — Botão gera link `wa.me` com mensagem pré-preenchida contendo todos os detalhes da fatura

---

## Regras de Negócio

### Cálculo da Fatura

| Consumo | Cálculo |
|---------|---------|
| Até **10 m³** (10.000 L) | Apenas taxa fixa (ex: R$ 25,00) |
| Acima de **10 m³** | Taxa fixa + (m³ excedentes × valor excedente) |

**Exemplo:**
```
Consumo: 15 m³ (15.000 L)
Taxa fixa:   R$ 25,00
Excedente:   5 m³ × R$ 2,00 = R$ 10,00
─────────────────────────────────────────
Total:       R$ 35,00
```

### Mensagem WhatsApp Gerada Automaticamente
```
Olá, [Nome]! 👋

📅 Referência: Junho / 2025
📊 Leitura anterior: 1.245,000 m³
📊 Leitura atual:    1.260,000 m³
💧 Consumo: 15,000 m³ (15.000 litros)
💰 Valor da fatura: R$ 35,00

Para pagamento ou dúvidas, entre em contato. Obrigado! 🙏
```

---

## Requisitos

- PHP **>= 8.2**
- Composer **>= 2.0**
- MySQL **>= 8.0**
- Extensões PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`

---

## Instalação e Configuração

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/sistema-agua.git
cd sistema-agua
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o ambiente

```bash
# Copie o arquivo de exemplo
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

### 4. Configure o banco de dados

Abra o arquivo `.env` e ajuste as credenciais do MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_agua
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

> **XAMPP:** O usuário padrão é `root` com senha vazia. Deixe `DB_PASSWORD=` em branco.

### 5. Crie o banco de dados

No **phpMyAdmin** ou qualquer cliente MySQL:

```sql
CREATE DATABASE sistema_agua CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Execute as migrations e o seeder

```bash
php artisan migrate --seed
```

Esse comando cria todas as tabelas e insere a configuração de taxa inicial:
- **Taxa fixa:** R$ 25,00
- **Valor excedente:** R$ 2,00/m³

### 7. Inicie o servidor local

```bash
php artisan serve
```

Acesse: **http://localhost:8000**

---

## Estrutura do Banco de Dados

```
consumidores
├── id
├── nome
├── endereco
├── telefone          ← Usado no link WhatsApp
├── numero_medidor    ← UNIQUE
└── timestamps

configuracoes_taxa
├── id
├── taxa_fixa         ← R$ 25,00 padrão
├── valor_excedente   ← R$ 2,00/m³ padrão
└── timestamps

leituras
├── id
├── consumidor_id     ← FK consumidores
├── mes_referencia    ← 1–12
├── ano_referencia
├── leitura_anterior  ← m³ (decimal 10,3)
├── leitura_atual     ← m³ (decimal 10,3)
├── consumo_m3        ← calculado: atual − anterior
└── timestamps
      UNIQUE(consumidor_id, mes_referencia, ano_referencia)

faturas
├── id
├── leitura_id        ← FK leituras
├── consumidor_id     ← FK consumidores
├── valor_total       ← R$ calculado
├── status            ← ENUM: pendente | pago
└── timestamps
```

---

## Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── ConsumidorController.php     ← CRUD de consumidores
│   ├── LeituraController.php        ← Registro + validações de negócio
│   ├── FaturaController.php         ← Listagem e marcar como pago
│   └── ConfiguracaoTaxaController.php ← Configuração de taxas
├── Models/
│   ├── Consumidor.php
│   ├── Leitura.php                  ← Accessor: getNomeMesAttribute()
│   ├── Fatura.php                   ← isPaga(), isPendente()
│   └── ConfiguracaoTaxa.php         ← ativa() static helper
└── Services/
    └── CobrancaService.php          ← Lógica de cálculo isolada

database/
├── migrations/
│   ├── ..._create_consumidores_table.php
│   ├── ..._create_configuracoes_taxa_table.php
│   ├── ..._create_leituras_table.php
│   └── ..._create_faturas_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── ConfiguracaoTaxaSeeder.php   ← Taxa padrão: R$25 / R$2

resources/views/
├── layouts/app.blade.php            ← Layout com sidebar
├── consumidores/{index,create,edit}.blade.php
├── leituras/{index,create}.blade.php
├── faturas/index.blade.php          ← Com botão WhatsApp
└── configuracao/index.blade.php     ← Calculadora dinâmica

routes/web.php                       ← Todas as rotas da aplicação
```

---

## Uso do Sistema

### Fluxo Principal

```
1. Cadastrar consumidor  →  /consumidores/create
2. Registrar leitura     →  /leituras/create
   (fatura gerada automaticamente)
3. Ver faturas do mês    →  /faturas
4. Clicar em WhatsApp    →  abre wa.me com mensagem pronta
5. Clicar em "Pago"      →  fatura marcada como paga
```

### Configuração de Taxa

Acesse `/configuracao` para alterar a **taxa fixa** e o **valor por m³ excedente**. O sistema exibe uma prévia do cálculo em tempo real conforme você digita.

---

## Commits

Histórico de commits seguindo o padrão [Conventional Commits](https://www.conventionalcommits.org/):

| Hash | Mensagem |
|------|----------|
| `a855553` | `chore: initial Laravel 13 project scaffold` |
| `3d6bbb3` | `feat: add ConsumidorController with full CRUD and validation` |
| `1712107` | `feat: register web routes for consumidores, leituras, faturas and configuracao` |
| `10e691a` | `feat: add FaturaController with monthly filter and pagar action` |
| `bdb057f` | `chore: scaffold LeituraController stub to wire routes` |
| `85263d6` | `feat: adiciona CobrancaService com calculo de taxa fixa e excedente` |
| `0faa299` | `feat: implementa LeituraController com validacoes de negocio e geracao de fatura` |
| `2b52992` | `test: adiciona script de teste para CobrancaService com 7 cenarios validados` |
| `b058cea` | `feat: cria views Blade com layout Bootstrap 5, sidebar e botao WhatsApp nas faturas` |

---

## Aplicação em produção

Acesse a aplicação hospedada na nuvem através do link abaixo:
**[https://seu-projeto.up.railway.app](https://seu-projeto.up.railway.app)**

### Credenciais de Teste

| Perfil | E-mail | Senha |
|---|---|---|
| **Gestor** | gestor@associacao.com.br | senha123 |
| **Leiturista** | leiturista@associacao.com.br | senha123 |

---

## Rodando localmente com Docker

Você pode rodar a aplicação inteira em containers isolados sem precisar instalar o PHP ou MySQL localmente.

### 1. Suba os containers

```bash
docker-compose up -d --build
```

### 2. Execute as migrations

```bash
docker-compose exec app php artisan migrate --seed
```

### 3. Acesse a aplicação

Acesse no navegador: **http://localhost:8000**

---

## Autores

**Gabriel Soares Vieira e Vanderson Vieira Lima**

---

## Licença

Este projeto foi desenvolvido para fins acadêmicos e comunitários.
