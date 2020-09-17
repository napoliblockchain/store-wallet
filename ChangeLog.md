#### 01.06.2020
Generazione Wallet per Istituto: organ captain goat crouch couple chest above rigid doctor recycle insect alien


## 05.12.2019

  # Modifiche da effettuare per l'integrazione sui server.

  Da questo promemoria va tirato fuori un elenco di operazioni da effettuare in
  tutta sicurezza e senza saltare processi fondamentali per il corretto funzionamento
  di tutta la webapp.
  Alcune tabelle servono al corretto funzionamento di tutte le applicazioni.

    - open new branch bolt_integration
    - cambi nomi classi

        - napay: :loadUser        Settings::loadUser
        - napay: :load            Settings::load
        - napay: :logo2              Logo::header
        - napay: :logo               Logo::login
        - napay: :footer             Logo::footer
        - utility: :encryptURl       crypt::Encrypt
        - utility: :decryptURl       crypt::Decrypt
        - Utility: :CountryDataset   Utils::CountryDataset
        - Utility: :passwordGenerator Utils::passwordGenerator
        - Utils: :ismobiledevice     Yii::app()->controller->isMobileDevice()
        - napay: :savesettings      Settings::save

    - rinominata tabella BOLT np_tokens in bolt_tokens e copiata in NAPAY (exNapos) db    
    (eliminare successivamente la tabella np_tokens di NaPay)
    - copiare la tabella bolt_tokens_memo in NAPAY (exNapos)
    - rinominata tabella BOLT np_contacts in bolt_contacts e copiata in NAPAY (exNapos) db    
    - aggiunto campo in settingsWebapp poa_decimals
    - ALTER TABLE `np_notifications_readers` ADD `readed` INT( 1 ) NOT NULL DEFAULT '0'
    - ALTER TABLE `np_users` CHANGE `ga_secret_key` `ga_secret_key` VARCHAR(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
    - UPDATE `np_users` SET `ga_secret_key` = NULL WHERE `np_users`.`ga_secret_key` = '';
    - copiare la tabella bolt_socialusers in NAPAY (exNapos)


                <!-- /**
                * Quando ci si registra su Napay, viene creata una row in bolt_socialusers e
                * in bolt_users  
                * oauth_provider deve essere:
                *     -  "merchant" per i commercianti
                *     -  "email" per i soci
                *
                * Nella fase di integrazione finale, fare attenzione agli id_user che nelle
                * tabelle np_users di wallet e bolt, devono essere differenti!
                *
                * Prendere a modello iniziale np_users di napay
                * su questo inserire gli user in bolt_users e bolt_socialusers
                * quindi inserire i vecchi np_users di bolt in bolt_users
                * aggiungere in bolt_socialusers i vecchi utenti con i nuovi id_user
                * aggiornare la tabella np_wallets con i nuovi id_user
                * aggiornare la tabella bolt_contacts con i nuovi id_user

                */*
                - la tabella np_wallets può rimanere con questo nome, ma deve essere unifor-
                mata a quella di BOLT. Vanno conservati gli id_user presenti in NaPay e
                aggiunti quelli di BOLT -->

    - la tabella np_wallets di bolt va rinominata in bolt_wallets e copiata in NAPAY (exNapos) db
    - la tabella originale np_wallets va uniformata con BOLT:
      - ALTER TABLE `np_wallets` DROP `wallet_key`, DROP `poa_url`, DROP `poa_port`;  
    - la tabella np_users va rinominata in bolt_users e copiata in NAPAY (exNapos) db
    - la tabella np_settings_user va rinominata in bolt_settings_user e copiata in NAPAY (exNapos) db
    - la tabella np_vapid_subscription va rinominata in bolt_vapid_subscription e copiata in NAPAY (exNapos) db


                <!-- - copiare la tabella np_vapid_subscription di BOLT in Napay (ha il campo type in più) !!! oppure
                ALTER TABLE `np_vapid_subscription` ADD `type` VARCHAR(20) NOT NULL AFTER `id_user`;
                /*TODO*/ Questo comporta modifica anche in Napay quando si accettano
                messaggi PUSH che in type scriverà "dashboard" -->

    - per quanto riguarda np_notifications in napay le notifiche vanno a id_merchant,
    mentre sarebbe più corretto inviarle a id_user. Quindi propoongo modifica
    della tabella id_merchant in id_user. Ovviamente per semplicità si può
    svuotare la tabella senza salvarne il contenuto.
      - ALTER TABLE `np_notifications` CHANGE `id_merchant` `id_user` INT(11) NOT NULL;
      - TRUNCATE TABLE `np_notifications`;
      - TRUNCATE TABLE `np_notifications_readers`;


    - imoprtare la tabella np_transactions_info di CASH-REGISTER in Napos  
    (da aggiornare ancora POS (x create invoice))





#### 05.08.2019
    - fixed bug in multiwallet

#### 01.08.2019
    - #270 : Aperto nuovo branch no-associazione

    - eliminato campo da np_merchants (id_association) [ALTER TABLE np_merchants DROP id_association;]
    - eliminati Controller Association, Merchants, Users, Userstytpe
    - eliminati Model Associations, DockerWallets
    - ripulite tutte le views e i controllers dove compariva Associations
    - Inserito nuovo controllo Login senza Privilegi di commerciante

    - Preparazione nuova funzione FastScan sulla blockchain


#### 12.07.2019
    - #239 : 2fa opzionale
    - adeguamento subscriptions

####01.07.2019
    [pinpad]
        - modificato css

####27.06.2019
    [issue]
        - #58

## 25.06.2019 Nuova versione 0.4
    [pin]
        - funzione di inserimento, controllo e rimozione del pin per l'accesso al wallet

#### 20.06.2019
    [swiper]
        - rimosso perchè rallenta troppo il sistema
    [push]
        - creato Comando `watchtower`  per gestire scansione transazioni su blockchain e inviare messaggi push ai
        possessori di wallet che abbiano effettuato la sottoscrizione
    [conferme]
        - visualizzazione conferme (240: numero di blocchi in 1 ora a 15 secondi)
    [balance]
        - visualizzazione balance del GAS, per evidenziare la possibilità di inviare o meno i TOKEN

#### 10.06.2019
    [swiper]
        - swipe pages like mobile phone

#### 31.05.2019
    [blockchain]
        - multiple transactions management

#### 30.05.2019
    [gas station]
        - lavorato su estimateGas

#### 29.05.2019
    [ERC20]
        - invio token con decrypt priv-key da js
        - storage priv-key
        - new logo
        - generazione e ripristino seed
        - fix sw
        - fix double ckick on send
        - fix modal dialog
        - fix clipboard copy
        - new function estimategas issue #38, #39
        - solved issue #34
        - solved issue #10


#### 28.05.2019
    - Gestione new seed
    - Gestione ripristino old seed
    - Storage wallet priv key

#### 27.05.2019 - new branch seed-js (with more js than php)
    [settings]
        - caricato lightwallet.min.js
        - caricato aes.js

#### 27.05.2019
    [Details]
        - aggiunto blockexplorer corretto per visualizzazione address e hash

    [Blockchain]    
        - ricerca su tutti gli indirizzi del wallet
        - block explorer senza sw

    [Settings]    
        - pulsante a scopmarsa

#### 26.05.2019
  [Ricevi token]
    - funzione di copia negli appunti indirizzo di Ricezione

  [ERC20 Send]
    - wip nonce ed altro

  [Check blockchain]
    - funzione ajax in caso non funziona sw

  [Commands]
    - Modificati in base a nuova funzionalità settings->poa_url

  [issues]
    - #22 Selezionando altro wallet deve cambiare qrcode di ricezione!

#### 23.05.2019
    [ERC20 Send]
        - nuova funzione con libreria ethereum-tx

    [Settings]
      - Fix Lista wallet: nel Model Wallet, wallet_address (unique). Da esportare
      in NaPay

#### 22.05.2019
    [createAccount]
        - new function createAccount offline

#### 20.05.2019
    [Vapid keys for push subscription]
        - vapid key website: https://web-push-codelab.glitch.me/
        - created Model for PushSubscription
        - put subscription just after user login
        - save user subscription on server

#### 15.05.2019
    [icone]
        - nuove icone - nuovo logo
    [Invio]
        - controllo invio decimali
        - salvataggio numero blocco al posto di balance
        - pulsante cliccabile (in corso/inviato)
    [Android APK]
        - creato apk android


#### 14.05.2019
    [issue]
        #19 : new row 'in corso' non è cliccabile
        #23 : Invio -> Invio token
        #28 : disable camera on annulla click
        #24 : Nuovo Balance non funziona sottrazione
        #20 : camera principale
        #3  : impostazioni:messaggio di conferma cambio Wallet predefinito
        #21 : Error 404 Il sistema non ha potuto trovare l'azione "about" richiesta.

    [sw]    
        Pulsante push rimanda ad history

#### 13.05.2019
    [Ricevi]
        issue #30 : disabilitare pulsante ricevi
        issue #29 : richiesta notifiche push all'vvio
        issue #25 : wallet predefinito tasto conferma a dx
        issue #17 : logout blocca app


#### 10.05.2019
    [SW]
        - Completato Blockchain search

#### 09.05.2019 New branch blockchain-search
    [SW]
        - cerca dall'ultimo blocco salvato in db le transazioni relative
            al wallet dell'utente
            ALTER TABLE `np_wallets` ADD `blocknumber` VARCHAR(50) NOT NULL DEFAULT '0x0' AFTER `poa_port`;

#### 09.05.2019
    [webcam]
        - Selezionando altra camera cambia visualizzazione front/rear
    [sw]            
        - no cache su history

#### 08.05.2019
    [Notifications]
        - Nuova gestione delle notifiche WebApp
    [WebCam]
        - Selezione della camera da utilizzare

#### 07.05.2019
    [ERC20]
        - Visualizzazione esclusiva dell'erc20 e non dell'ether


#### 07.05.2019
    [ERC20 Transactions]
        - Risolto problema di salvataggio importo in ricezione ERC20
        - issue #16 : transazioni cliccabili sui pulsanti
        - cambio colori 'invio' 'ricezione' ecc.
        - Riaggiustato command send & receive per eth e erc20
        - Issue #6 : RICEVI: se si riceve più volte senza fare il refresh della pagina l'ultimo prezzo sovrascrive anche i precedenti


#### 06.05.2019
    [Wallet history]
        - issue #14 : dataProvider criteria must show all transactions
        - issue #12 : menu history al 100%
        - fixing camera qrcode for android
        - issue #8 : id wallet criptato

#### 03.05.2019
    - Nuovo Pulsante 'Abilitazione Messaggi Push' in Settings.
    - Predisposizione per libreria Messaggi Push da Server
    - web-push-php libreria installata

#### 30.04.2019
    - Login: number field to google 2fa
    - syncing check txpool
    - error messages on send
    - issue #9 : cambiare chiavi private cryptoURL

#### 29.04.2019
    - issue #7 : notifications view- il link riporta alla cartella wallet-tts.

###V 0.2.0 - 23.04.2019
    - versione 0.2.2 stable

####17.04.2019
    - push subscriptions
    - listening push messages
    - send push messages from server

####16.04.2019
    #151 Storing Subscription
     setup npm firebase-tools firebase-admin web-push
     firebase deploy  

####15.04.2019
    - issue #4 : solved: impostazioni: nuovo Wallet generato deve essere wrapped se lenght »200px
    - Web push notification
        - Requesting permission
        - Showing notification
        - using  VAPID with Web Push

#### 12.04.2019
    - Background Sync...
        - Optimizing best caching strategy
        - Syncing Data in the Service Worker

#### 11.04.2019
    - IndexedDB and Dynamic Data
        - Firebase
        - Storing Fetched Posts in IndexedDB
        - IndexedDB and Caching Strategies
    - Creating a Responsive User Interface (UI)
    - Background Sync
        - Registering a Synchronization Task
        - Storing our Post in IndexedDB

#### 10.04.2019
    - Service worker
    - Promise and Fetch
    - Caching
        - Dynamic caching
        - Static caching
        - Optimize caching management: best practice
    - Advanced Caching
        - Offline Fallback page
        - Cache with network fallback

#### 09.04.2019
    - issue #1 : rimuovere footer
    - issue #2 : transazioni non ha css card

#### 08.04.2019
    - node module installed
    - manifest.json
    - pwa Service Worker
    - App Install Banner

#### 26.03.2019
    - Google 2FA Login
    - Layout solo per mobile
    - Invio Token e Eth
    - Ricezione Token e Eth
    - Lista Transazioni
    - Dettaglio Transazioni
    - Creazione Nuovo Wallet
    - Selezione del wallet in uso

    - PWA Progressive Web App
        https://medium.com/dev-channel/learn-how-to-build-a-pwa-in-under-5-minutes-c860ad406ed

    - Google reCaptcha
    - Real time Balance     


### V 0.0.1 - 25.03.2019
    Initial Release
