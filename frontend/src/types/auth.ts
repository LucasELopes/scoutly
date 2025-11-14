export interface AuthResponse {
  access_token: string;
  expires_in: number;
  token_type: string;
}

export interface AuthOptions {
  email: string;
  password: string;
}

export interface AuthErrorResponse {
  message: string;
}