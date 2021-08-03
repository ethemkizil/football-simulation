Merhabalar;

İstenilen üzerine 6 hafta 3 maç 4 takım futbol smilasyonu hazırladım, tablolar için migration ekledim.

```
php artisan migrate
```

Takımların rastgele güçlerle oluşturulması için seeder ekledim

```
php artisan db:seed
```

Api Dalları

```
api/teams
```
Takım listesini döndürür

```
api/generate-fixture
```
Takımları 6 haftalık peryoda göre eşleştirir, bir kez ev sahibi ve bir kez demplasman olacak şekilde 

```
api/play-week
```
Geçerli Haftanın maçlarını simüle ederek oynatır sonuçları puan çetveline atar  

```
api/play-all-week
```
Tüm haftaları simüle ederek oynatır sonuçları puan çetveline atar

```
api/score-sheet
```
Aktif Puan çetvelini döndürür

```
api/reset-data
```
Tüm verileri sıfırlar
