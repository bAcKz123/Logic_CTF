# Logic CTF Lab

Đây là lab CTF web đơn giản để luyện tư duy khai thác lỗi logic.

## Mục tiêu

- Đăng nhập tài khoản người dùng.
- Chơi game để kiếm coin.
- Mua item và tìm flag.

## Yêu cầu

- Docker
- Docker Compose

## Cài đặt nhanh

```bash
git clone <your-repo-url>
cd Logic_CTF
docker compose up -d --build
```

Truy cập: `http://localhost:5000`

## Lệnh thường dùng

```bash
# Chạy lab
docker compose up -d

# Dừng lab
docker compose down
```

## Lưu ý

- Không để lộ các file nhạy cảm như `db.sql`, `.env`, `Dockerfile`.
- Lab chỉ dùng cho mục đích học tập.
