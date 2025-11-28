# 🧪 Test Docker Build Secara Lokal

Cara test Dockerfile sebelum push ke Render.

## Prerequisites

1. **Docker Desktop** terinstall dan running
   - Download: https://www.docker.com/products/docker-desktop
   - Atau install via Homebrew: `brew install --cask docker`

2. **Verifikasi Docker**:
   ```bash
   docker --version
   docker ps
   ```

## Cara Test

### Opsi 1: Quick Test (Build Only)

Test apakah Dockerfile bisa di-build tanpa error:

```bash
docker build -t desa-donoharjo:test .
```

**Expected**: Build berhasil tanpa error.

---

### Opsi 2: Full Test (Build + Run)

Gunakan script yang sudah disediakan:

```bash
./test-docker-local.sh
```

Script ini akan:
1. Build Docker image
2. Run container dengan test command
3. Cek apakah config dan migration bisa jalan

---

### Opsi 3: Test dengan Database Real

Jika punya PostgreSQL lokal:

```bash
# Build image
docker build -t desa-donoharjo:test .

# Run dengan database lokal
docker run --rm -p 8000:8000 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=host.docker.internal \
  -e DB_PORT=5432 \
  -e DB_DATABASE=nama_database \
  -e DB_USERNAME=username \
  -e DB_PASSWORD=password \
  -e CACHE_DRIVER=file \
  -e SESSION_DRIVER=file \
  -e PORT=8000 \
  desa-donoharjo:test
```

**Note**: `host.docker.internal` untuk akses PostgreSQL di host machine dari dalam container.

---

### Opsi 4: Test Syntax Only (Tanpa Docker)

Cek syntax Dockerfile tanpa build:

```bash
# Install hadolint (Dockerfile linter)
brew install hadolint

# Lint Dockerfile
hadolint Dockerfile
```

Atau gunakan online linter: https://hadolint.github.io/hadolint/

---

## Troubleshooting

### Docker tidak running
```bash
# Start Docker Desktop
open -a Docker

# Atau via command line (jika Docker Desktop sudah terinstall)
docker info
```

### Build error: "Cannot connect to Docker daemon"
- Pastikan Docker Desktop running
- Cek: `docker ps`

### Build terlalu lama
- Normal, pertama kali build bisa 10-15 menit
- Build berikutnya lebih cepat karena Docker layer caching

### Port sudah digunakan
```bash
# Cek port yang digunakan
lsof -i :8000

# Atau gunakan port lain
docker run -p 8001:8000 ...
```

---

## Checklist Sebelum Push

- [ ] Docker build berhasil tanpa error
- [ ] Container bisa start (meskipun tanpa database)
- [ ] Tidak ada syntax error di Dockerfile
- [ ] File `.dockerignore` sudah benar
- [ ] Tidak ada file SQLite yang ter-copy

---

## Tips

1. **Build cepat untuk test syntax**:
   ```bash
   docker build --target builder -t test .
   ```

2. **Cek size image**:
   ```bash
   docker images desa-donoharjo:test
   ```

3. **Cek layer Dockerfile**:
   ```bash
   docker history desa-donoharjo:test
   ```

4. **Clean up setelah test**:
   ```bash
   docker rmi desa-donoharjo:test
   docker system prune -f
   ```

---

**Setelah test lokal berhasil, baru push ke Render!** 🚀

