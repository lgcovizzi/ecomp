export interface Instituicao {
  id: number
  nome_longo: string
  nome_curto: string
  enderecos?: Endereco[]
  registros_count?: number
  created_at: string
  updated_at: string
}

export interface Endereco {
  id: number
  titulo: string
  cidade: string
  estado: string
  instituicao_id: number
  instituicao?: Instituicao
  departamentos?: Departamento[]
  registros_count?: number
  created_at: string
  updated_at: string
}

export interface Departamento {
  id: number
  nome: string
  instituicao_id: number
  endereco_id: number
  instituicao?: Instituicao
  endereco?: Endereco
  registros_count?: number
  created_at: string
  updated_at: string
}

export interface Registro {
  id: number
  nome: string
  instituicao_id?: number
  endereco_id?: number
  departamento_id?: number
  instituicao?: Instituicao
  endereco?: Endereco
  departamento?: Departamento
  created_at: string
  updated_at: string
}

export interface ApiResponse<T> {
  data: T
  message?: string
  errors?: any
}