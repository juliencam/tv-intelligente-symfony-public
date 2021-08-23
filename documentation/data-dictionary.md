# data dictionary

## post

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT||
|title|VARCHAR(255)|NOT NULL| |
|content|LONGTEXT|NULL| |
|iframe|text|NOT NULL, UNIQUE|product price|
|uriimage|VARCHAR(255)|NOT NULL, UNIQUE|uri of the image file|
|urlimage|VARCHAR(255)|NOT NULL, UNIQUE|url of the image on the server|
|created_at|DATETIME|NOT NULL, ON UPDATE||
|updated_at|DATETIME|NULL, ON UPDATE||
|youtuber_id|INT|FOREIGN KEY, UNSIGNED, INDEX| |

---

## category

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT||
|name|VARCHAR(255)|NOT NULL, UNIQUE| |

---

## ATTACHED
|Champ|Type|Specificities|Description|
|-|-|-|-|
|post_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |
|category_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |

---

## youtuber

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT||
|name|VARCHAR(255)|NOT NULL| |
|uriimage|VARCHAR(255)|NOT NULL, UNIQUE|uri of the image file|
|urlimage|VARCHAR(255)|NOT NULL, UNIQUE|url of the image on the server|
|urlchanel|VARCHAR(255)|NOT NULL, UNIQUE||
|created_at|DATETIME|NOT NULL, ON UPDATE||
|updated_at|DATETIME|NULL, ON UPDATE||

---

## tag

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT||
|name|VARCHAR(255)|NOT NULL, UNIQUE| |

---

## RELATED
|Champ|Type|Specificities|Description|
|-|-|-|-|
|post_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |
|tag_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |

---

## postlike

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT||
|user_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |
|post_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |

---

## user

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT|user id|
|email|VARCHAR(255)|NOT NULL, UNIQUE||
|password|VARCHAR(255)|NOT NULL||
|pseudonym|VARCHAR(64)|NOT NULL, UNIQUE||
|uriimage|VARCHAR(255)|NOT NULL, UNIQUE|uri of the image file|
|urlimage|VARCHAR(255)|NOT NULL, UNIQUE|url of the image on the server|
|status|smallint|NOT NULL, UNSIGNED, DEFAULT 0|status of user (0=actif, 1=inactif 2=banned)|
|role|text|NOT NULL, DEFAULT ["ROLE_USER"]|role (DC2Type:json)|
|created_at|DATETIME|NOT NULL, ON UPDATE||
|updated_at|DATETIME|NULL, DEFAULT ON UPDATE||

---

## comment

|Champ|Type|Specificities|Description|
|-|-|-|-|
|id|INT|PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT||
|content|text|NOT NULL| |
|status|smallint|NOT NULL, UNSIGNED, DEFAULT 0| (0=actif, 1=banned|
|user_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |
|post_id|INT|PRIMARY KEY, FOREIGN KEY, UNSIGNED, INDEX| |
|created_at|DATETIME|NOT NULL, ON UPDATE||
|updated_at|DATETIME|NULL, DEFAULT ON UPDATE||
