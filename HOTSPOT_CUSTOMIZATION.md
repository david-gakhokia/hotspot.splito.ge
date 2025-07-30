# Splito Hotspot სისტემის კონფიგურაცია

## ახალი ფუნქციონალი

### 1. Tower Group რეკლამის ბანერი
- **მდებარეობა**: login გვერდის ზედა ნაწილში
- **ფუნქციონალი**: ღილაკზე დაჭერით იხსნება https://towergroup.ge ახალ ფანჯარაში
- **სურათი**: `public/hotspot/images/tower-group-banner.jpg` (ოპტიმალური ზომა: 400x120px)
- **Analytics**: თვალყურის დევნება რეკლამის impression და click-ებზე

### 2. სწრაფი შესვლის ღილაკები
- **სტუმარი**: username="guest", password=""
- **უფასო WiFi**: username="free", password="wifi"
- **ავტომატური**: თუ ჩართულია "ავტომატური ინტერნეტის ჩართვა", ავტომატურად ასრულებს login-ს

### 3. ავტომატური კავშირი
- **Checkbox**: მომხმარებელს შეუძლია ჩართოს/გამორთოს
- **LocalStorage**: ინახავს პრეფერენციას browser-ში
- **ფლოუ**: login-ის შემდეგ:
  1. ავტომატურად ჩნდება Tower Group modal 5 წამით
  2. შემდეგ გადაამისამართებს splito.ge-ზე

### 4. Analytics და Tracking
- **Google Analytics** მზადდ (gtag integration)
- **Events**: 
  - `hotspot_page_view`
  - `ad_impression`
  - `ad_click` 
  - `login_attempt`
  - `login_success`
  - `quick_login_used`
  - `auto_connect_toggled`

## MikroTik კონფიგურაცია

### Router OS სკრიპტი
```routeros
# Hotspot HTML Files განახლება
/ip hotspot walled-garden
add dst-host=towergroup.ge
add dst-host=splito.ge

# Hotspot ფაილების ატვირთვა
/tool fetch url="http://your-server.com/hotspot/login.html" dst-path=hotspot/login.html
/tool fetch url="http://your-server.com/hotspot/css/styles.css" dst-path=hotspot/styles.css
/tool fetch url="http://your-server.com/hotspot/js/app.js" dst-path=hotspot/app.js
```

### ფაილების სტრუქტურა
```
public/hotspot/
├── login.html          # მთავარი login გვერდი
├── css/
│   └── styles.css      # სტილები (ახალი ad banner სტილებით)
├── js/
│   └── app.js          # JavaScript (ახალი ფუნქციონალით)
└── images/
    ├── logo.png        # Splito ლოგო
    ├── tower-group-banner.jpg  # Tower Group ბანერი
    └── tower-group-modal.jpg   # Modal-ისთვის სურათი
```

## გამოყენება

### 1. სურათების ჩანაცვლება
1. ჩაანაცვლეთ `tower-group-banner.jpg` რეალური სურათით
2. დაამატეთ `tower-group-modal.jpg` modal-ისთვის
3. დარწმუნდით რომ სურათები არის optimized web-ისთვის

### 2. Domain კონფიგურაცია
- შეცვალეთ `https://towergroup.ge` რეალური Tower Group domain-ით
- დაამატეთ domain walled garden-ში MikroTik-ზე

### 3. Analytics Setup
```javascript
// Google Analytics-ის დამატება <head> სექციაში
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_TRACKING_ID');
</script>
```

### 4. ტესტირება
1. გახსენით `public/hotspot/login.html` browser-ში
2. შეამოწმეთ რეკლამის ბანერი
3. ტესტი სწრაფი login ღილაკები
4. შეამოწმეთ auto-connect ფუნქციონალი

## მომავალი განვითარება

### შესაძლო გაუმჯობესებები:
1. **A/B Testing**: სხვადასხვა რეკლამის ბანერების ტესტირება
2. **Personalization**: მომხმარებლის history-ის მიხედვით რეკლამების ჩვენება
3. **Timer**: real-time session timer
4. **Multiple Ads**: მრავალი რეკლამოდატელი rotation-ით
5. **Offline Support**: service worker-ით offline functionality

### Performance ოპტიმიზაცია:
- Image lazy loading
- CSS/JS minification  
- CDN integration
- Caching strategies
