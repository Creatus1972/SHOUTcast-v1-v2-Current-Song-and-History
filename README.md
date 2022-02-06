# SHOUTcast-v1-v2-Current-Song-and-History
SHOUTcast szerver aktuális zeneszám és lejátszási előzmények megjelenítése weboldalon.

Néhány alapvető információ!

Az aktuális zeneszám, illetve a lejátszási előzmények CSS formázással 
vannak megjelenítve fokozva ezzel a látványt, és mellőzve a szkriptek használatát.

A lejátstási előzményekből szintén CSS - el ki lett vágva az aktuális zeneszám...ez bármikor megjeleníthető az alábbi kód törlésével:

/* Current Song - Display */
#history table tr:nth-child(2) {
    display: none !important;
}
/* Current Song - Display */

Mint látható, a kódban erre külön van egy komment a kezdő fejlesztők számára.

Egyes információkat akaratlanul is megjelenít a PHP szkript, ezért az str_replace - vel lett kivágva.

==============================  SZERZŐI JOGI KÖZLEMÉNY  =================================
   
Az eredeti licensz Thomas Kroll ShoutCAST DNAS v2 adatelemző licence a Creative Commons 
(Oszd meg! Nevezd meg!) 4.0 nemzetközi licenc alapján készült, melynek linkje már nem érhető el.
Az újradolgozásért Bujdosó Lajos felelős a https://www.mcomp.hu/song.php.txt 
címen található munka alapján.
   
==============================  CREATIVE COMMON LICENSZ  ================================

A Creative Common licensz magyar verziója a https://mcomp.hu/cc/cc.pdf címről tölthető le.
Eredeti verzió a https://creativecommons.org/licenses/by-sa/4.0/ címen érhető el.

====================================================================================
