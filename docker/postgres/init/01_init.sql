-- PostgreSQL 16 — İlk kurulum scripti
-- Bu dosya container ilk başlatıldığında bir kez çalışır

-- UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- pg_crypto extension (şifreleme için)
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- pg_trgm extension (LIKE aramalarını hızlandırır)
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- unaccent extension (Türkçe karakter aramak için)
CREATE EXTENSION IF NOT EXISTS "unaccent";
