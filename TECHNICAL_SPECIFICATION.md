# TEXNIK TOPSHIRIQ (TZ)
## Maktablar uchun Universal Telegram Bot va Admin Panel

**Versiya:** 2.0  
**Sana:** 27.04.2026  
**Loyiha:** Laravel Universal School Bot

---

## 📋 LOYIHA HAQIDA

Maktablar uchun to'liq funksional Telegram bot va admin panel tizimi. Har bir maktab o'z botini yaratib, mustaqil boshqarishi mumkin (multi-tenant arxitektura).

### Asosiy Maqsad
Maktablarning Telegram orqali o'quvchilar, ota-onalar va abituriyentlar bilan muloqotini avtomatlashtirish va soddalashtirish.

---

## 🎯 ASOSIY FUNKSIYALAR

### 1. BOT FUNKSIYALARI

#### 1.1 Asosiy Menyu
Bot ishga tushganda quyidagi menyu ko'rsatiladi:

```
🏫 Maktab haqida
📋 Vakansiyalar  
🏆 Olimpiadalar
🎓 Qabul
📢 E'lonlar
⚙️ Sozlamalar
```

**Ikonkalar:**
- 🏫 Maktab haqida
- 📋 Vakansiyalar
- 🏆 Olimpiadalar
- 🎓 Qabul
- 📢 E'lonlar
- ⚙️ Sozlamalar
- 📞 Aloqa
- 📍 Manzil
- 👨‍🏫 O'qituvchilar
- 📚 Fanlar

#### 1.2 Maktab Haqida (YANGI)
**Status:** ❌ Yo'q (qo'shish kerak)

**Funksiyalar:**
- Maktab tarixi va missiyasi
- Direktor va rahbariyat haqida
- Maktab yutuqlari va sertifikatlari
- Foto galereya
- Video taqdimot
- Kontakt ma'lumotlar
- Manzil va xarita
- Ish vaqti

**Database struktura:**
```sql
school_info:
  - id
  - school_id (FK)
  - about_text (longText)
  - history_text (longText)
  - mission_text (text)
  - vision_text (text)
  - director_message (text)
  - achievements (json)
  - gallery_images (json)
  - video_url (string)
  - contact_phone (string)
  - contact_email (string)
  - address (text)
  - map_latitude (decimal)
  - map_longitude (decimal)
  - working_hours (json)
  - social_links (json)
  - created_at, updated_at
```

**Bot oqimi:**
```
User: "🏫 Maktab haqida"
Bot: [Inline keyboard]
  - 📖 Umumiy ma'lumot
  - 👨‍💼 Rahbariyat
  - 🏆 Yutuqlar
  - 📸 Galereya
  - 📹 Video
  - 📞 Kontaktlar
  - 📍 Manzil
  - ⬅️ Orqaga
```

#### 1.3 Vakansiyalar
**Status:** ✅ Mavjud (to'ldirish kerak)

**Hozirgi holat:**
- ✅ Vakansiyalar ro'yxati
- ✅ Ariza topshirish (FIO, yosh, telefon, tajriba)
- ❌ CV yuklash
- ❌ Foto yuklash
- ❌ Email qo'shish

**Qo'shish kerak:**
- CV fayl yuklash (PDF, DOC, DOCX)
- Foto yuklash
- Email manzil
- Kategoriya bo'yicha filter (o'qituvchi, xodim, boshqa)
- Fan bo'yicha filter

**Bot oqimi (yangilangan):**
```
1. Vakansiya tanlash
2. To'liq ism → Email → Telefon → Yosh
3. Tajriba → Tajriba yillari → Fan/Yo'nalish
4. CV yuklash (ixtiyoriy)
5. Foto yuklash (ixtiyoriy)
6. Tasdiqlash va yuborish
```

#### 1.4 Olimpiadalar
**Status:** ✅ Mavjud (to'ldirish kerak)

**Hozirgi holat:**
- ✅ Olimpiadalar ro'yxati
- ✅ Ro'yxatdan o'tish
- ❌ To'lov tizimi (agar pullik bo'lsa)
- ❌ Natijalarni ko'rish

**Qo'shish kerak:**
- To'lov integratsiyasi (Click, Payme)
- Natijalar bo'limi
- Sertifikat yuklash
- Qayta ro'yxatdan o'tish imkoniyati

#### 1.5 Qabul
**Status:** ⚠️ Qisman (to'liq qilish kerak)

**Hozirgi holat:**
- ✅ Qabul ma'lumotlari ko'rsatiladi
- ❌ Ariza topshirish yo'q

**Qo'shish kerak:**
- To'liq ariza topshirish tizimi
- O'quvchi ma'lumotlari
- Ota-ona ma'lumotlari
- Hujjatlar yuklash
- Ariza holati kuzatuvi

**Bot oqimi (yangi):**
```
1. Qabul kampaniyasini tanlash
2. O'quvchi FIO → Tug'ilgan sana → Jinsi
3. Qaysi sinfga → Oldingi maktab
4. Ota-ona FIO → Telefon → Telefon 2 (ixtiyoriy)
5. Ota-ona aloqasi (ota/ona/vasiy)
6. Manzil
7. Hujjatlar yuklash (ixtiyoriy)
8. Tasdiqlash va yuborish
```

**Database qo'shimchalar:**
```sql
admission_applications jadvali mavjud, lekin bot handler yo'q
```

#### 1.6 E'lonlar
**Status:** ✅ Mavjud

**Hozirgi holat:**
- ✅ E'lonlar ro'yxati
- ✅ Broadcast xabarlar
- ❌ Media yuklash (foto, video)
- ❌ Inline tugmalar

**Qo'shish kerak:**
- Media yuborish (foto, video, hujjat)
- Inline tugmalar qo'shish
- Scheduled broadcast (vaqt bo'yicha)

#### 1.7 Sozlamalar (YANGI)
**Status:** ❌ Yo'q (qo'shish kerak)

**Funksiyalar:**
- Til tanlash (O'zbek/Rus)
- Bildirishnomalar sozlamalari
- Profil ma'lumotlari
- Mening arizalarim
- Yordam

**Bot oqimi:**
```
User: "⚙️ Sozlamalar"
Bot: [Inline keyboard]
  - 🌐 Til: O'zbek 🇺🇿
  - 🔔 Bildirishnomalar: Yoniq ✅
  - 👤 Profil
  - 📝 Mening arizalarim
  - ❓ Yordam
  - ⬅️ Orqaga
```

---

### 2. KO'P TILLILIK (MULTILINGUAL)

**Status:** ❌ Yo'q (qo'shish kerak)

**Tillar:**
- 🇺🇿 O'zbek (asosiy)
- 🇷🇺 Rus

**Implementatsiya:**
```php
// Database struktura
user_preferences:
  - id
  - telegram_user_id (FK)
  - language (enum: 'uz', 'ru')
  - notifications_enabled (boolean)
  - created_at, updated_at

// Lang fayllar
resources/lang/uz/bot.php
resources/lang/ru/bot.php
```

**Tarjima kerak bo'lgan qismlar:**
- Barcha bot xabarlari
- Tugma matnlari
- Xato xabarlari
- Muvaffaqiyat xabarlari
- Validatsiya xabarlari

**Til almashtirish:**
```
User: "⚙️ Sozlamalar" → "🌐 Til"
Bot: Tilni tanlang:
  [🇺🇿 O'zbek] [🇷🇺 Русский]
```

---

### 3. ADMIN PANEL

#### 3.1 Dashboard
**Status:** ✅ Mavjud

**Statistika:**
- Maktablar soni
- Foydalanuvchilar soni
- Faol vakansiyalar
- Faol olimpiadalar
- Qabul kampaniyalari
- E'lonlar soni
- Majburiy kanallar

**Qo'shish kerak:**
- Grafiklar (Chart.js)
- Oxirgi arizalar
- Oxirgi ro'yxatdan o'tishlar
- Faollik grafigi (kunlik/haftalik)

#### 3.2 Maktab Haqida (YANGI)
**Status:** ❌ Yo'q

**CRUD operatsiyalar:**
- Umumiy ma'lumot tahrirlash
- Rahbariyat ma'lumotlari
- Yutuqlar qo'shish/o'chirish
- Galereya boshqaruvi
- Video URL
- Kontaktlar
- Xarita koordinatalari
- Ish vaqti

#### 3.3 Sozlamalar
**Status:** ✅ Mavjud

**Mavjud:**
- Bot token
- Welcome text
- Main menu text
- Menu buttons

**Qo'shish kerak:**
- Default til
- Bildirishnomalar sozlamalari
- Bot avatar
- Bot tavsifi

#### 3.4 Vakansiyalar
**Status:** ✅ Mavjud

**Qo'shish kerak:**
- Arizalarni export (Excel)
- Bulk actions (ko'plab arizalarni bir vaqtda o'zgartirish)
- Email yuborish (ariza holati o'zgarganda)
- SMS yuborish (ixtiyoriy)

#### 3.5 Olimpiadalar
**Status:** ✅ Mavjud

**Qo'shish kerak:**
- Natijalarni import (Excel)
- Sertifikatlar generatsiya qilish
- Ro'yxatdan o'tganlarni export
- To'lovlarni kuzatish

#### 3.6 Qabul
**Status:** ⚠️ Qisman

**Qo'shish kerak:**
- Arizalarni ko'rish va boshqarish
- Ariza holati o'zgartirish
- Hujjatlarni ko'rish
- Export funksiyasi
- Statistika

#### 3.7 E'lonlar
**Status:** ✅ Mavjud

**Qo'shish kerak:**
- Media yuklash interfeysi
- Inline tugmalar qo'shish
- Scheduled broadcast
- Recurring broadcast
- Yuborish statistikasi (ko'rildi, bosildi)

#### 3.8 Kanallar
**Status:** ✅ Mavjud

**Yaxshilash:**
- Kanal a'zolarini tekshirish
- Kanal statistikasi

#### 3.9 Foydalanuvchilar (YANGI)
**Status:** ❌ Yo'q

**Funksiyalar:**
- Barcha foydalanuvchilar ro'yxati
- Filter (obunachi/obuna emas, faol/nofaol)
- Search (ism, username, telefon)
- Foydalanuvchi profili
- Foydalanuvchi arizalari
- Foydalanuvchiga xabar yuborish
- Export

#### 3.10 Hisobotlar (YANGI)
**Status:** ❌ Yo'q

**Hisobotlar:**
- Kunlik/haftalik/oylik statistika
- Vakansiya arizalari hisoboti
- Olimpiada ro'yxatdan o'tishlari
- Qabul arizalari
- Foydalanuvchilar faolligi
- E'lonlar samaradorligi

---

## 🗄️ DATABASE STRUKTURASI

### Yangi Jadvallar

#### 1. school_info
```sql
CREATE TABLE school_info (
    id BIGINT PRIMARY KEY,
    school_id BIGINT FK,
    about_text LONGTEXT,
    history_text LONGTEXT,
    mission_text TEXT,
    vision_text TEXT,
    director_name VARCHAR(255),
    director_photo VARCHAR(255),
    director_message TEXT,
    achievements JSON,
    gallery_images JSON,
    video_url VARCHAR(255),
    contact_phone VARCHAR(50),
    contact_email VARCHAR(100),
    address TEXT,
    map_latitude DECIMAL(10,8),
    map_longitude DECIMAL(11,8),
    working_hours JSON,
    social_links JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### 2. user_preferences
```sql
CREATE TABLE user_preferences (
    id BIGINT PRIMARY KEY,
    telegram_user_id BIGINT FK,
    language ENUM('uz', 'ru') DEFAULT 'uz',
    notifications_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(telegram_user_id)
);
```

#### 3. bot_translations
```sql
CREATE TABLE bot_translations (
    id BIGINT PRIMARY KEY,
    key VARCHAR(255),
    language VARCHAR(10),
    value TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(key, language)
);
```

### Mavjud Jadvallarni Yangilash

#### vacancies jadvali
```sql
ALTER TABLE vacancies ADD COLUMN category_icon VARCHAR(50);
ALTER TABLE vacancies ADD COLUMN is_featured BOOLEAN DEFAULT FALSE;
```

#### vacancy_applications jadvali
- ✅ Barcha kerakli ustunlar mavjud

#### olympiads jadvali
```sql
ALTER TABLE olympiads ADD COLUMN certificate_template VARCHAR(255);
ALTER TABLE olympiads ADD COLUMN results_file VARCHAR(255);
```

#### announcements jadvali
- ✅ Media va inline buttons uchun ustunlar mavjud

---

## 🎨 UI/UX TALABLARI

### Bot Interfeysi

**Tugmalar dizayni:**
```
[🏫 Maktab haqida] [📋 Vakansiyalar]
[🏆 Olimpiadalar]   [🎓 Qabul]
[📢 E'lonlar]       [⚙️ Sozlamalar]
```

**Inline tugmalar:**
- Har bir bo'limda "⬅️ Orqaga" tugmasi
- "✅ Tasdiqlash" va "❌ Bekor qilish" tugmalari
- "📄 Ko'proq" tugmasi (uzun matnlar uchun)

**Xabar formatlari:**
- Sarlavhalar: **Bold**
- Muhim ma'lumotlar: *Italic*
- Ro'yxatlar: • Bullet points
- Raqamlar: 1️⃣ 2️⃣ 3️⃣

### Admin Panel

**Dizayn:**
- ✅ Mavjud Admin template ishlatiladi
- Responsive dizayn
- Dark mode (ixtiyoriy)

**Ranglar:**
- Primary: Blue (#007bff)
- Success: Green (#28a745)
- Warning: Yellow (#ffc107)
- Danger: Red (#dc3545)

---

## 🔧 TEXNIK TALABLAR

### Backend
- **Framework:** Laravel 13
- **PHP:** 8.3+
- **Database:** MySQL 8.0+ / PostgreSQL 14+
- **Queue:** Redis (broadcast uchun)
- **Cache:** Redis
- **Storage:** Local / S3 (media uchun)

### Telegram
- **SDK:** irazasyed/telegram-bot-sdk ^3.16
- **Webhook:** HTTPS required
- **Bot API:** Latest version

### Frontend
- **Build tool:** Vite 8.0
- **CSS:** Tailwind CSS 4.0
- **JS:** Vanilla JS / Alpine.js (ixtiyoriy)

### Xavfsizlik
- CSRF protection
- XSS prevention
- SQL injection prevention
- Rate limiting
- Input validation
- File upload validation

---

## 📝 IMPLEMENTATSIYA REJASI

### Faza 1: Asosiy Funksiyalar (1-2 hafta)

**1.1 Maktab Haqida bo'limi**
- [ ] Migration yaratish (school_info)
- [ ] Model va Controller
- [ ] Admin CRUD interfeysi
- [ ] Bot handler
- [ ] Inline keyboard navigation
- [ ] Media yuklash (foto, video)

**1.2 Ko'p tillilik**
- [ ] Migration (user_preferences, bot_translations)
- [ ] Lang fayllar (uz, ru)
- [ ] Middleware (language detection)
- [ ] Til almashtirish funksiyasi
- [ ] Barcha matnlarni tarjima qilish

**1.3 Sozlamalar bo'limi**
- [ ] Bot handler
- [ ] Profil ko'rish
- [ ] Mening arizalarim
- [ ] Yordam sahifasi

### Faza 2: Qabul Tizimi (1 hafta)

**2.1 Qabul arizalari**
- [ ] Bot conversation flow
- [ ] Hujjat yuklash
- [ ] Ariza saqlash
- [ ] Admin interfeysi
- [ ] Ariza holati o'zgartirish
- [ ] Bildirishnomalar

### Faza 3: Yaxshilashlar (1 hafta)

**3.1 Vakansiyalar**
- [ ] CV yuklash
- [ ] Foto yuklash
- [ ] Email qo'shish
- [ ] Export funksiyasi

**3.2 Olimpiadalar**
- [ ] Natijalar import
- [ ] Sertifikat generatsiya
- [ ] To'lov integratsiyasi (ixtiyoriy)

**3.3 E'lonlar**
- [ ] Media yuborish
- [ ] Inline tugmalar
- [ ] Scheduled broadcast
- [ ] Statistika

### Faza 4: Admin Panel (1 hafta)

**4.1 Dashboard**
- [ ] Grafiklar qo'shish
- [ ] Real-time statistika
- [ ] Oxirgi faoliyat

**4.2 Foydalanuvchilar**
- [ ] Ro'yxat sahifasi
- [ ] Filter va search
- [ ] Profil sahifasi
- [ ] Export

**4.3 Hisobotlar**
- [ ] Statistika sahifasi
- [ ] Excel export
- [ ] PDF export (ixtiyoriy)

### Faza 5: Testing va Optimizatsiya (1 hafta)

**5.1 Testing**
- [ ] Unit tests
- [ ] Feature tests
- [ ] Bot testing
- [ ] Load testing

**5.2 Optimizatsiya**
- [ ] Query optimization
- [ ] Cache implementation
- [ ] Image optimization
- [ ] Code refactoring

**5.3 Dokumentatsiya**
- [ ] API dokumentatsiya
- [ ] Foydalanuvchi qo'llanmasi
- [ ] Admin qo'llanmasi
- [ ] Deployment qo'llanmasi

---

## 🧪 TESTING REJASI

### Bot Testing
- /start komandasi
- Har bir menyu bo'limi
- Conversation flow
- Majburiy obuna
- Xato holatlari
- Til almashtirish

### Admin Panel Testing
- Login/Logout
- CRUD operatsiyalar
- File upload
- Export funksiyalari
- Permissions
- Responsive dizayn

### Performance Testing
- 1000+ foydalanuvchi
- Broadcast 10000+ xabar
- Database query optimization
- Cache effectiveness

---

## 📊 SUCCESS METRICS

### Bot Metrics
- Foydalanuvchilar soni
- Kunlik faol foydalanuvchilar (DAU)
- Ariza topshirish konversiyasi
- Majburiy obuna konversiyasi
- O'rtacha session davomiyligi

### Admin Metrics
- Ariza qayta ishlash vaqti
- Admin faolligi
- Export foydalanish
- Broadcast muvaffaqiyat darajasi

---

## 🚀 DEPLOYMENT

### Server Talablari
- **CPU:** 2+ cores
- **RAM:** 4GB+
- **Storage:** 50GB+ SSD
- **OS:** Ubuntu 22.04 LTS

### Services
- Nginx / Apache
- PHP-FPM
- MySQL / PostgreSQL
- Redis
- Supervisor (queue worker)

### SSL Certificate
- Let's Encrypt (bepul)
- Cloudflare (ixtiyoriy)

---

## 📞 SUPPORT VA MAINTENANCE

### Monitoring
- Server monitoring (CPU, RAM, Disk)
- Application monitoring (errors, logs)
- Bot monitoring (webhook status)
- Database monitoring (queries, connections)

### Backup
- Daily database backup
- Weekly full backup
- Media files backup
- Backup retention: 30 days

### Updates
- Security patches
- Laravel updates
- Telegram Bot API updates
- Dependencies updates

---

## 💰 BUDGET ESTIMATION (Ixtiyoriy)

### Development
- Faza 1: 40 soat
- Faza 2: 20 soat
- Faza 3: 20 soat
- Faza 4: 20 soat
- Faza 5: 20 soat
- **Jami:** 120 soat

### Infrastructure (oylik)
- Server: $20-50
- Domain: $10-15/yil
- SSL: $0 (Let's Encrypt)
- Backup storage: $5-10
- **Jami:** $25-60/oy

---

## 📋 CHECKLIST

### Must Have (Majburiy)
- [x] Maktab haqida bo'limi
- [x] Ko'p tillilik (uz, ru)
- [x] Qabul arizalari tizimi
- [x] Vakansiya CV yuklash
- [x] Olimpiada natijalar
- [x] E'lonlar media
- [x] Sozlamalar bo'limi
- [x] Foydalanuvchilar bo'limi
- [x] Export funksiyalari

### Nice to Have (Qo'shimcha)
- [ ] To'lov integratsiyasi
- [ ] SMS bildirishnomalar
- [ ] Push notifications
- [ ] Mobile app
- [ ] Chatbot AI
- [ ] Analytics dashboard
- [ ] Multi-school mobile app

---

## 🎯 XULOSA

Bu TZ maktablar uchun to'liq funksional, zamonaviy va foydalanuvchilarga qulay Telegram bot va admin panel tizimini yaratish uchun mo'ljallangan.

**Asosiy ustunliklar:**
- ✅ Multi-tenant (ko'p maktabli)
- ✅ Ko'p tillilik
- ✅ To'liq CRUD operatsiyalar
- ✅ Media yuklash
- ✅ Export funksiyalari
- ✅ Real-time statistika
- ✅ Xavfsiz va optimallashtirilgan

**Keyingi qadamlar:**
1. TZ ni tasdiqlash
2. Development boshlash
3. Testing
4. Deployment
5. Training va support

---

**Tayyorlagan:** Claude AI  
**Sana:** 27.04.2026  
**Versiya:** 2.0
