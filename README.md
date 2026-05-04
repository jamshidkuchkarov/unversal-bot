# Laravel Universal School Bot

Bu loyiha maktab uchun universal Telegram bot va admin panel skeleti.

## Nimalar bor

- Maktab haqidagi sahifalarni admin paneldan boshqarish
- O`qituvchilar uchun vakansiyalar CRUD
- Olimpiada yaratish, boshlanish va tugash vaqtini saqlash
- Majburiy obuna kanallarini admin o`zi almashtira olishi
- Telegram foydalanuvchilarini bazaga yozish
- Hamma yoki faqat obunachilarga broadcast yuborish
- Clean code asosida kengayadigan Laravel struktura

## Ishga tushirish

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Admin panel:

- URL: `/login`
- Login: `admin@example.com`
- Parol: `password`

## Telegram sozlash

`.env` ichiga quyidagilarni kiriting:

```env
APP_TIMEZONE=Asia/Tashkent
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_WEBHOOK_URL=https://your-domain.com/telegram/webhook
```

Webhook ulash:

```bash
php artisan telegram:webhook:set
```

## Admin panel bo`limlari

- `Bot sozlamalari`: welcome text, aloqa, maktab tavsifi
- `Sahifalar`: bot menyusida chiqadigan bo`limlar
- `Vakansiyalar`: o`qituvchi ish o`rinlari
- `Olimpiadalar`: sarlavha, vaqt, link
- `Majburiy obuna`: kanal username, chat id, invite link
- `Xabar yuborish`: broadcast xabarlar

## Muhim eslatma

- Bot bog`cha uchun emas, maktab yo`nalishi uchun tuzilgan.
- Majburiy obuna ishlashi uchun bot kanalda admin bo`lishi kerak.
- `chat_id` sifatida odatda kanalning manfiy ID si ishlatiladi.
- Broadcast hozir admin paneldan `queued` holatga o`tkazilganda darhol yuboriladi.

## Keyingi kengaytirishlar

- Queue orqali scheduled broadcast
- Excel eksport
- Role va permission lar
- Media fayl yuborish
- Ko`p maktabli multi-tenant rejim
