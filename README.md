# Kurumsal AI Asistan Platformu                                                              
       2 +                                                                                             
       3 +Docker tabanlı full-stack web uygulaması:                                                    
       4 +- **Backend**: Symfony 7.x (PHP 8.3)                                                         
       5 +- **Frontend**: Angular 19+ (Node 22 LTS)                                                    
       6 +- **Veritabani**: PostgreSQL 16                                                              
       7 +- **Cache**: Redis 7                                                                         
       8 +- **Web Server**: Nginx 1.27                                                                 
       9 +                                                                                             
      10 +## Servisler ve Portlar                                                                      
      11 +                                                                                             
      12 +| Servis       | URL                      | Aciklama                       |                 
      13 +|--------------|--------------------------|-------------------------------|                  
      14 +| Nginx (proxy)| http://localhost          | Ana giris noktasi              |                
      15 +| Angular      | http://localhost:4200     | Angular dev server (dogrudan)  |                
      16 +| Symfony API  | http://localhost/api      | Backend API (nginx uzerinden)  |                
      17 +| Mailhog      | http://localhost:8025     | E-posta test UI                |                
      18 +| pgAdmin      | http://localhost:5050     | PostgreSQL yonetim paneli      |                
      19 +| PostgreSQL   | localhost:5432            | Veritabani                     |                
      20 +| Redis        | localhost:6379            | Cache                          |                
      21 +                                                                                             
      22 +## Hizli Baslangic                                                                           
      23 +                                                                                             
      24 +```bash                                                                                      
      25 +# 1. Repo'yu klonla ve env dosyasini hazirla                                                 
      26 +cp .env.example .env                                                                         
      27 +                                                                                             
      28 +# 2. Ilk kurulumu yap (image build + container basalt)                                       
      29 +make install                                                                                 
      30 +                                                                                             
      31 +# 3. Loglari takip et (Symfony ve Angular kurulumunu izle)                                   
      32 +make logs                                                                                    
      33 +```                                                                                          
      34 +                                                                                             
      35 +> Not: Ilk acilista Symfony ve Angular projeleri otomatik kurulur.                           
      36 +> Bu islem internet hizina bagli olarak 3-10 dakika surebilir.                               
      37 +                                                                                             
      38 +## Gelistirme Komutlari                                                                      
      39 +                                                                                             
      40 +```bash                                                                                      
      41 +make help              # Tum komutlari listele                                               
      42 +make shell-php         # PHP container'ina baglan                                            
      43 +make shell-node        # Angular container'ina baglan                                        
      44 +make migrate           # Veritabani migration calistir                                       
      45 +make cache-clear       # Symfony cache temizle                                               
      46 +make logs              # Tum loglari izle                                                    
      47 +```                                                                                          
      48 +                                                                                             
      49 +## Proje Yapisi                                                                              
      50 +                                                                                             
      51 +```                                                                                          
      52 +backend/           - Symfony 7.x (otomatik olusturulur)                                      
      53 +frontend/          - Angular 19+ (otomatik olusturulur)                                      
      54 +docker/                                                                                      
      55 +  php/             - PHP 8.3 FPM Dockerfile + config                                         
      56 +  nginx/           - Nginx config                                                            
      57 +  node/            - Node 22 Dockerfile + entrypoint                                         
      58 +  postgres/        - PostgreSQL init scriptleri                                              
      59 +docker-compose.yml                                                                           
      60 +docker-compose.prod.yml                                                                      
      61 +.env               - Ortam degiskenleri (git'e eklenmez)                                     
      62 +.env.example       - Ornek ortam degiskenleri                                                
      63 +Makefile           - Gelistirici komutlari    