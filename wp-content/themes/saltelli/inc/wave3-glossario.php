<?php
/**
 * WAVE3 TASK 9 — /glossario-legale/ render block.
 *
 * Layout sacro replicato dal JSX:
 *   .claude/knowledge/design/sessione-2/saltelli-s2-glossario-legale.jsx
 *
 * Hero 5/7 · sticky search+a-z · <dl> 30/70 · FAQ details/summary · CTA.
 * Schema JSON-LD: DefinedTermSet + DefinedTerm × 60 + FAQPage emessi inline.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

// Map categoria umana → slug competenza esistente (per link "Aree correlate").
$sl_gloss_cat_url = static function ($label) {
    $map = [
        'Diritto tributario'        => 'diritto-tributario',
        'Diritto del lavoro'        => 'diritto-del-lavoro',
        'Diritto di famiglia'       => 'diritto-di-famiglia',
        'Famiglia LGBTQ+'           => 'diritto-di-famiglia-lgbtq',
        'Cartelle e riscossione'    => 'cartelle-esattoriali-e-multe',
        'Diritto condominiale'      => 'diritto-condominiale',
        'Successioni'               => 'diritto-delle-successioni',
        'Diritto penale'            => 'diritto-penale',
        'Diritto bancario'          => 'diritto-bancario',
        'Diritto previdenziale'     => 'diritto-previdenziale',
        'Recupero crediti'          => 'recupero-crediti',
        'Risarcimento danni'        => 'risarcimento-danni',
        'Responsabilità civile'     => 'responsabilita-civile',
    ];
    if (!isset($map[$label])) {
        return '';
    }
    $page_obj = get_page_by_path($map[$label], OBJECT, 'competenza');
    $url = $page_obj ? get_permalink($page_obj) : '';
    return $url ?: home_url('/competenze/' . $map[$label] . '/');
};

$sl_gloss_terms = [
    ['l' => 'A', 'k' => 'accertamento-sintetico', 't' => 'Accertamento sintetico', 'cat' => 'Tributario',
     'def' => "Metodo presuntivo di determinazione del reddito basato sulla disponibilità di beni e servizi indici di capacità contributiva. La presunzione è legale relativa: l'onere della prova grava sul contribuente, che può dimostrare la non rilevanza fiscale delle spese sostenute.",
     'esempio' => "Acquisto di un'auto da €60.000 con un reddito dichiarato di €25.000 può attivare un controllo redditometrico.",
     'correlate' => ['Diritto tributario']],
    ['l' => 'A', 'k' => 'affidamento-condiviso', 't' => 'Affidamento condiviso', 'cat' => 'Famiglia',
     'def' => "Regime di esercizio della responsabilità genitoriale a seguito di separazione, in cui entrambi i genitori partecipano alle decisioni rilevanti per il figlio. È la regola; l'affidamento esclusivo è l'eccezione, motivata dall'interesse del minore.",
     'esempio' => "Decisioni su scuola, salute, residenza prese insieme, anche con collocamento prevalente presso un genitore.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'A', 'k' => 'affidamento-esclusivo', 't' => 'Affidamento esclusivo', 'cat' => 'Famiglia',
     'def' => "Regime in cui la responsabilità genitoriale è attribuita a un solo genitore, mentre l'altro conserva il diritto di vigilanza e contributo economico. Il giudice lo dispone solo quando l'affidamento condiviso risulta contrario all'interesse del minore.",
     'esempio' => "Genitore con condotte violente o gravi inadempienze: il giudice affida i figli all'altro genitore.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'A', 'k' => 'agenzia-entrate-riscossione', 't' => 'Agenzia delle Entrate-Riscossione', 'cat' => 'Tributario',
     'def' => "Ente pubblico economico subentrato a Equitalia dal 1° luglio 2017 nelle funzioni di riscossione coattiva di tributi, contributi previdenziali e sanzioni. Notifica cartelle esattoriali, dispone fermi e ipoteche, gestisce le rateizzazioni e i pagamenti.",
     'esempio' => "Cartella per IRPEF non versata notificata da AdE-Riscossione: opposizione entro 60 giorni davanti al giudice tributario.",
     'correlate' => ['Cartelle e riscossione']],
    ['l' => 'A', 'k' => 'appello', 't' => 'Appello', 'cat' => 'Processo',
     'def' => "Mezzo di impugnazione che consente la revisione della sentenza di primo grado da parte di un giudice superiore. In materia civile si propone alla Corte d'Appello entro 30 giorni dalla notifica o 6 mesi dalla pubblicazione della sentenza.",
     'esempio' => "Sentenza del Tribunale di Napoli del 10 gennaio: appello entro 10 luglio (termine lungo) o 30 gg dalla notifica.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'A', 'k' => 'arbitrato', 't' => 'Arbitrato', 'cat' => 'Civile',
     'def' => "Procedimento alternativo alla giustizia ordinaria in cui le parti devolvono la decisione di una controversia a uno o più arbitri privati. Il lodo arbitrale ha forza di sentenza ma deve essere depositato per ottenere efficacia esecutiva. Spesso usato in materia commerciale.",
     'esempio' => "Clausola compromissoria in un contratto: la controversia va decisa da un collegio arbitrale, non dal tribunale.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'A', 'k' => 'assegno-mantenimento', 't' => 'Assegno di mantenimento', 'cat' => 'Famiglia',
     'def' => "Contributo economico periodico che un coniuge è tenuto a versare all'altro o ai figli dopo la separazione, per garantirne il livello di vita. L'importo è determinato dal giudice in base a redditi, patrimoni e contributo dato al ménage familiare.",
     'esempio' => "Coniuge con reddito da €60k versa €600/mese al coniuge con reddito da €18k, oltre al mantenimento per i due figli.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'A', 'k' => 'assegno-divorzile', 't' => 'Assegno divorzile', 'cat' => 'Famiglia',
     'def' => "Contributo economico previsto a favore del coniuge economicamente più debole dopo lo scioglimento del matrimonio. Le Sezioni Unite 18287/2018 hanno introdotto un criterio composito assistenziale-perequativo-compensativo, superando il \"tenore di vita\".",
     'esempio' => "Coniuge che ha rinunciato alla carriera per crescere i figli: ha diritto all'assegno divorzile in funzione perequativa.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'A', 'k' => 'assemblea-condominiale', 't' => 'Assemblea condominiale', 'cat' => 'Condominio',
     'def' => "Organo deliberativo del condominio che decide su gestione ordinaria, straordinaria, modifiche regolamentari. Si convoca obbligatoriamente almeno una volta l'anno (assemblea ordinaria) ed è presieduta da un condomino eletto dai presenti.",
     'esempio' => "Delibera per rifacimento facciata adottata in assemblea: vincolante anche per i condomini assenti, salva impugnazione.",
     'correlate' => ['Diritto condominiale']],
    ['l' => 'A', 'k' => 'atto-citazione', 't' => 'Atto di citazione', 'cat' => 'Civile',
     'def' => "Atto introduttivo del processo civile ordinario, con cui l'attore cita in giudizio il convenuto davanti al tribunale. Contiene i fatti, le ragioni della domanda, le conclusioni. Va notificato almeno 90 giorni prima dell'udienza fissata (120 se all'estero).",
     'esempio' => "Citazione per il pagamento di un credito di €15.000: udienza fissata a 4 mesi dalla notifica.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'A', 'k' => 'autotutela', 't' => 'Autotutela', 'cat' => 'Tributario',
     'def' => "Potere dell'amministrazione finanziaria di annullare, in tutto o in parte, un atto impositivo viziato anche dopo che è divenuto definitivo. Si attiva su istanza del contribuente o d'ufficio. Strumento utile prima del ricorso al giudice tributario.",
     'esempio' => "Avviso di accertamento basato su un errore di calcolo evidente: istanza di autotutela all'Agenzia delle Entrate per ottenere annullamento.",
     'correlate' => ['Diritto tributario']],
    ['l' => 'A', 'k' => 'avviso-bonario', 't' => 'Avviso bonario', 'cat' => 'Tributario',
     'def' => "Comunicazione con cui l'Agenzia delle Entrate segnala irregolarità rilevate dal controllo automatizzato delle dichiarazioni. Consente il pagamento ridotto entro 30 giorni con sanzione al 10%. Non impugnabile autonomamente; precede l'iscrizione a ruolo.",
     'esempio' => "Avviso bonario per omesso versamento IRPEF 2024: pagamento entro 30 gg con sanzione 10% anziché 30%.",
     'correlate' => ['Diritto tributario']],
    ['l' => 'C', 'k' => 'cartella-esattoriale', 't' => 'Cartella esattoriale', 'cat' => 'Tributario',
     'def' => "Atto con cui l'Agenzia delle Entrate-Riscossione richiede al contribuente il pagamento di somme iscritte a ruolo (imposte, contributi, sanzioni). Va impugnata davanti alla Corte di giustizia tributaria entro 60 giorni dalla notifica. Decorso inutile, diviene titolo esecutivo.",
     'esempio' => "Cartella per IRPEF non versata 2019: impugnazione possibile per vizio di notifica o prescrizione quinquennale.",
     'correlate' => ['Cartelle e riscossione']],
    ['l' => 'C', 'k' => 'cassazione', 't' => 'Corte di Cassazione', 'cat' => 'Processo',
     'def' => "Organo di vertice della giurisdizione ordinaria, giudice di legittimità (non di merito). Decide su violazioni di legge, vizi procedurali, motivazione contraddittoria. Il ricorso è ammesso solo per i motivi tassativi dell'art. 360 c.p.c.",
     'esempio' => "Riforma di una sentenza d'appello per omessa motivazione su un motivo decisivo della controversia.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'C', 'k' => 'condominio', 't' => 'Condominio', 'cat' => 'Condominio',
     'def' => "Forma di proprietà collettiva su un edificio in cui le parti comuni (scale, tetto, facciate, impianti) appartengono pro quota a tutti i condomini. Disciplinato dagli artt. 1117 ss. c.c. e dalla legge 220/2012 di riforma.",
     'esempio' => "Edificio con 8 appartamenti: ogni proprietario è condomino delle parti comuni in proporzione ai millesimi.",
     'correlate' => ['Diritto condominiale']],
    ['l' => 'C', 'k' => 'contraddittorio', 't' => 'Contraddittorio', 'cat' => 'Processo',
     'def' => "Principio costituzionale (art. 111 Cost.) per cui ogni parte del processo ha diritto di essere sentita e di replicare alle ragioni avversarie prima della decisione del giudice. La sua violazione comporta la nullità del processo.",
     'esempio' => "Sentenza emessa senza dare al convenuto il tempo di costituirsi: nulla per difetto di contraddittorio.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'C', 'k' => 'contratto-a-termine', 't' => 'Contratto a termine', 'cat' => 'Lavoro',
     'def' => "Rapporto di lavoro subordinato con scadenza prefissata. Dopo il dlgs 81/2015 (modificato dal Decreto Dignità) richiede causale per superare i 12 mesi e ha un limite massimo di 24 mesi sommando proroghe e rinnovi.",
     'esempio' => "Contratto a termine 18 mesi senza causale: nullo. Conversione in tempo indeterminato dal primo giorno.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'C', 'k' => 'contumacia', 't' => 'Contumacia', 'cat' => 'Processo',
     'def' => "Mancata costituzione di una parte regolarmente citata in giudizio. Il processo prosegue ugualmente; le difese non svolte non possono più essere sollevate, salve le eccezioni rilevabili d'ufficio. La sentenza va notificata al contumace per decorrere il termine breve d'impugnazione.",
     'esempio' => "Convenuto non si costituisce alla prima udienza: dichiarato contumace, processo prosegue senza di lui.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'C', 'k' => 'convivenza-di-fatto', 't' => 'Convivenza di fatto', 'cat' => 'Famiglia',
     'def' => "Istituto introdotto dalla legge 76/2016 che disciplina i diritti di due persone maggiorenni unite stabilmente da legami affettivi e reciproca assistenza, non vincolate da matrimonio o unione civile. Riconoscimento più limitato rispetto alle altre due forme.",
     'esempio' => "Coppia eterosessuale convivente da 8 anni: diritto di subentrare nel contratto di locazione e nell'eredità con quota residuale.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'C', 'k' => 'ctu', 't' => 'CTU (consulenza tecnica)', 'cat' => 'Processo',
     'def' => "Consulenza Tecnica d'Ufficio: indagine specialistica disposta dal giudice quando la decisione richiede competenze tecniche. Il consulente è ausiliario del giudice e redige una relazione che, pur non vincolante, ha valore di prova. Le parti possono nominare un proprio CTP.",
     'esempio' => "Causa per responsabilità medica: il giudice nomina un medico-legale per accertare il nesso causale.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'D', 'k' => 'decadenza', 't' => 'Decadenza', 'cat' => 'Civile',
     'def' => "Estinzione di un diritto per il mancato esercizio entro un termine perentorio fissato dalla legge o dal contratto. Si distingue dalla prescrizione perché non ammette interruzione né sospensione (salvo eccezioni) e va rilevata d'ufficio se prevista per interesse pubblico.",
     'esempio' => "Termine di 60 giorni per impugnare un licenziamento: superato, decade il diritto di contestarlo in giudizio.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'D', 'k' => 'decreto-ingiuntivo', 't' => 'Decreto ingiuntivo', 'cat' => 'Civile',
     'def' => "Provvedimento sommario con cui il giudice ordina al debitore di pagare una somma o consegnare una cosa, su richiesta del creditore che produca prova scritta del credito. Si oppone entro 40 giorni dalla notifica; in caso contrario diventa titolo esecutivo.",
     'esempio' => "Fattura non pagata da €8.000: il creditore ottiene decreto ingiuntivo in 30 giorni; debitore può opporsi entro 40 gg.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'D', 'k' => 'demansionamento', 't' => 'Demansionamento', 'cat' => 'Lavoro',
     'def' => "Assegnazione del lavoratore a mansioni inferiori rispetto al livello contrattuale, vietata salvo eccezioni dall'art. 2103 c.c. (modificato dal Jobs Act). Genera diritto al risarcimento del danno professionale, biologico e all'immagine.",
     'esempio' => "Dirigente assegnato a compiti meramente esecutivi senza giustificato motivo: demansionamento risarcibile.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'D', 'k' => 'dimissioni-giusta-causa', 't' => 'Dimissioni per giusta causa', 'cat' => 'Lavoro',
     'def' => "Recesso del lavoratore da un rapporto di lavoro per condotta del datore così grave da non consentirne la prosecuzione. Danno diritto al preavviso non lavorato e all'indennità NASpI, a differenza delle dimissioni volontarie ordinarie.",
     'esempio' => "Mancato pagamento di tre mensilità: il lavoratore si dimette per giusta causa con diritto a NASpI.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'E', 'k' => 'esecuzione-immobiliare', 't' => 'Esecuzione immobiliare', 'cat' => 'Civile',
     'def' => "Procedura giudiziale con cui il creditore munito di titolo esecutivo soddisfa il proprio credito attraverso la vendita all'asta dell'immobile del debitore. Si articola in pignoramento, vendita, distribuzione del ricavato. Disciplinata dal Libro III del c.p.c.",
     'esempio' => "Mutuo non rimborsato per oltre 18 rate: la banca avvia esecuzione immobiliare sull'appartamento ipotecato.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'F', 'k' => 'ferie-maturate', 't' => 'Ferie maturate', 'cat' => 'Lavoro',
     'def' => "Periodo di riposo annuale retribuito a cui ha diritto ogni lavoratore subordinato. Il minimo di legge è 4 settimane (dlgs 66/2003), elevabile dal CCNL. Le ferie non godute alla cessazione del rapporto vanno monetizzate.",
     'esempio' => "Lavoratore licenziato con 18 giorni di ferie residue: ha diritto al pagamento sostitutivo in busta paga finale.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'F', 'k' => 'fermo-amministrativo', 't' => 'Fermo amministrativo', 'cat' => 'Tributario',
     'def' => "Misura cautelare con cui Agenzia delle Entrate-Riscossione blocca l'utilizzo di un veicolo intestato al debitore. Si attiva dopo la cartella non pagata e va preceduto da preavviso. Impugnabile per vizi davanti al giudice ordinario o tributario secondo materia.",
     'esempio' => "Auto sottoposta a fermo per cartella INPS non pagata: il debitore non può circolare né cederla finché non salda.",
     'correlate' => ['Cartelle e riscossione']],
    ['l' => 'I', 'k' => 'interpello', 't' => 'Interpello', 'cat' => 'Tributario',
     'def' => "Istanza con cui il contribuente chiede all'Agenzia delle Entrate il parere su un caso concreto e personale di interpretazione di norme tributarie. La risposta vincola l'amministrazione, non il contribuente. Esistono diverse tipologie (ordinario, probatorio, anti-abuso, disapplicativo).",
     'esempio' => "Holding che chiede se un'operazione di scissione configura abuso del diritto: interpello anti-abuso.",
     'correlate' => ['Diritto tributario']],
    ['l' => 'I', 'k' => 'ipoteca-esattoriale', 't' => 'Ipoteca esattoriale', 'cat' => 'Tributario',
     'def' => "Garanzia reale che Agenzia delle Entrate-Riscossione iscrive sui beni immobili del debitore quando l'importo della cartella supera €20.000. Va preceduta da comunicazione preventiva. Si estingue con il pagamento o con la prescrizione del credito.",
     'esempio' => "Iscrizione ipotecaria su appartamento per debito €25.000: il proprietario non può venderlo senza saldare o ottenere riduzione.",
     'correlate' => ['Cartelle e riscossione']],
    ['l' => 'L', 'k' => 'legato', 't' => 'Legato', 'cat' => 'Successioni',
     'def' => "Disposizione testamentaria con cui il testatore attribuisce a un soggetto un singolo bene o diritto specifico, a differenza dell'erede che subentra nell'universalità del patrimonio. Si acquista automaticamente alla morte del testatore, salvo rinuncia.",
     'esempio' => "Testamento: «lascio a mio nipote Marco l'orologio del nonno». Marco è legatario, non erede.",
     'correlate' => ['Successioni']],
    ['l' => 'L', 'k' => 'legittima', 't' => 'Legittima', 'cat' => 'Successioni',
     'def' => "Quota di eredità riservata per legge ai legittimari: coniuge, figli, ascendenti. Non può essere lesa dalle disposizioni testamentarie del defunto. La lesione consente l'azione di riduzione entro 10 anni dall'apertura della successione.",
     'esempio' => "Padre lascia tutto a un'amica via testamento: i figli possono agire in riduzione per recuperare la quota di legittima.",
     'correlate' => ['Successioni']],
    ['l' => 'L', 'k' => 'licenziamento-giusta-causa', 't' => 'Licenziamento per giusta causa', 'cat' => 'Lavoro',
     'def' => "Recesso datoriale fondato su una condotta del lavoratore così grave da non consentire la prosecuzione, anche provvisoria, del rapporto. Esclude il preavviso. Sindacabile dal giudice sotto il profilo della gravità e della proporzionalità.",
     'esempio' => "Sottrazione di beni aziendali, violenza sul luogo di lavoro, abbandono del posto in fasi critiche.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'L', 'k' => 'licenziamento-gmo', 't' => 'Licenziamento per giustificato motivo', 'cat' => 'Lavoro',
     'def' => "Licenziamento intimato per ragioni soggettive (notevole inadempimento del lavoratore) o oggettive (esigenze produttive, organizzative). Richiede preavviso. La giurisprudenza esige proporzionalità tra causa addotta e sanzione espulsiva.",
     'esempio' => "Riduzione dell'organico per chiusura di un reparto: licenziamento per gmo, con onere della prova al datore di lavoro.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'M', 'k' => 'mansioni', 't' => 'Mansioni', 'cat' => 'Lavoro',
     'def' => "Insieme di compiti che il lavoratore è tenuto a svolgere in base al contratto e alla qualifica di inquadramento. L'art. 2103 c.c. consente l'assegnazione a mansioni equivalenti o, eccezionalmente, inferiori in caso di modifica organizzativa.",
     'esempio' => "Impiegato assunto come «addetto contabilità»: non può essere stabilmente adibito a mansioni di pulizia.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'M', 'k' => 'mediazione-obbligatoria', 't' => 'Mediazione obbligatoria', 'cat' => 'Civile',
     'def' => "Procedimento di conciliazione previsto come condizione di procedibilità per alcune materie (locazione, condominio, successioni, contratti bancari). La parte che agisce in giudizio deve prima tentare l'accordo davanti a un organismo accreditato.",
     'esempio' => "Causa condominiale per ripartizione spese: senza tentativo di mediazione il giudice dichiara l'improcedibilità.",
     'correlate' => ['Diritto condominiale']],
    ['l' => 'M', 'k' => 'mobbing', 't' => 'Mobbing', 'cat' => 'Lavoro',
     'def' => "Condotta persecutoria sistematica posta in essere dal datore o da colleghi nei confronti del lavoratore, finalizzata a emarginarlo o farlo dimettere. Genera diritto al risarcimento del danno biologico, esistenziale, professionale. Onere probatorio rigoroso a carico della vittima.",
     'esempio' => "Lavoratrice esclusa dalle riunioni, demansionata, isolata fisicamente per mesi: ipotesi di mobbing verticale.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'N', 'k' => 'naspi', 't' => 'NASpI', 'cat' => 'Previdenziale',
     'def' => "Nuova Assicurazione Sociale per l'Impiego: indennità di disoccupazione introdotta dal Jobs Act per i lavoratori che hanno perso involontariamente il lavoro. Spetta a chi ha almeno 13 settimane di contributi negli ultimi 4 anni; durata massima 24 mesi.",
     'esempio' => "Lavoratore licenziato dopo 5 anni: domanda NASpI all'INPS entro 68 giorni, riceve circa il 75% dell'ultima retribuzione.",
     'correlate' => ['Diritto previdenziale']],
    ['l' => 'N', 'k' => 'negoziazione-assistita', 't' => 'Negoziazione assistita', 'cat' => 'Famiglia',
     'def' => "Procedura introdotta dal d.l. 132/2014 con cui le parti, tramite i rispettivi avvocati, raggiungono un accordo che ha lo stesso valore di un provvedimento giudiziale. Utilizzata anche per separazioni e divorzi senza figli minori, con omologa del Procuratore.",
     'esempio' => "Separazione consensuale tra coniugi senza figli minori: accordo via negoziazione assistita, evita il tribunale.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'N', 'k' => 'notifica', 't' => 'Notifica', 'cat' => 'Processo',
     'def' => "Atto formale con cui un atto giuridico viene portato a conoscenza del destinatario, con valore certo della data e del soggetto raggiunto. Eseguita da ufficiale giudiziario, posta raccomandata o, oggi, anche via PEC. Vizi di notifica determinano nullità.",
     'esempio' => "Atto di citazione notificato via PEC a indirizzo non risultante da pubblici elenchi: nullità della notifica.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'O', 'k' => 'opposizione-decreto-ingiuntivo', 't' => 'Opposizione a decreto ingiuntivo', 'cat' => 'Civile',
     'def' => "Atto di citazione con cui il debitore contesta il decreto ingiuntivo ricevuto, aprendo un giudizio ordinario in cui le posizioni si invertono (l'opponente è formalmente attore ma sostanzialmente convenuto). Termine perentorio: 40 giorni dalla notifica.",
     'esempio' => "Decreto ingiuntivo per fattura contestata: il debitore propone opposizione eccependo l'inadempimento del fornitore.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'P', 'k' => 'pignoramento', 't' => 'Pignoramento', 'cat' => 'Civile',
     'def' => "Primo atto della procedura esecutiva, con cui l'ufficiale giudiziario individua e vincola i beni del debitore destinati a soddisfare il creditore. Può essere mobiliare, immobiliare o presso terzi (stipendio, conto corrente).",
     'esempio' => "Stipendio pignorato fino a un quinto netto: il datore versa quota direttamente al creditore procedente.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'P', 'k' => 'prescrizione', 't' => 'Prescrizione', 'cat' => 'Civile',
     'def' => "Estinzione del diritto per il decorso del tempo unito all'inerzia del titolare. Termine ordinario decennale (art. 2946 c.c.). Termini brevi quinquennali per fitti, retribuzioni, danni da illecito, cartelle di tributi locali.",
     'esempio' => "Cartella notificata nel 2018, mai sollecitata: prescritta nel 2023 per i contributi previdenziali (cinque anni).",
     'correlate' => ['Diritto tributario', 'Recupero crediti']],
    ['l' => 'P', 'k' => 'prima-udienza', 't' => 'Prima udienza', 'cat' => 'Processo',
     'def' => "Udienza di trattazione e comparizione delle parti davanti al giudice (art. 183 c.p.c.). In questa sede il giudice verifica il contraddittorio, tenta la conciliazione, fissa il calendario delle attività istruttorie e i termini per il deposito delle memorie.",
     'esempio' => "Prima udienza fissata in citazione: le parti possono modificare le domande e chiedere termini per memorie istruttorie.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'R', 'k' => 'rateizzazione', 't' => 'Rateizzazione', 'cat' => 'Tributario',
     'def' => "Possibilità di pagare a rate i debiti iscritti a ruolo presso Agenzia delle Entrate-Riscossione. Fino a 72 rate (6 anni) automatica; fino a 120 rate (10 anni) con prova della temporanea difficoltà. Decadenza dopo otto rate non pagate, anche non consecutive.",
     'esempio' => "Cartella da €30.000: rateizzazione a 72 rate da €450 mensili; otto rate insolute fanno decadere il piano.",
     'correlate' => ['Cartelle e riscossione']],
    ['l' => 'R', 'k' => 'ravvedimento-operoso', 't' => 'Ravvedimento operoso', 'cat' => 'Tributario',
     'def' => "Strumento che consente al contribuente di sanare omessi o ritardati versamenti riducendo le sanzioni, prima dell'avviso di accertamento. La riduzione varia dal 1/10 al 1/5 del minimo a seconda del momento del ravvedimento.",
     'esempio' => "IVA non versata a luglio: ravvedimento entro 14 gg con sanzione 1/10 del 30% (= 0,1% al giorno).",
     'correlate' => ['Diritto tributario']],
    ['l' => 'R', 'k' => 'ricorso', 't' => 'Ricorso', 'cat' => 'Processo',
     'def' => "Atto introduttivo o impugnatorio con cui si chiede al giudice una pronuncia. Si distingue dalla citazione perché si deposita prima ed è il giudice a fissare l'udienza. Forma tipica del processo amministrativo, tributario, del lavoro e di Cassazione.",
     'esempio' => "Ricorso contro avviso di accertamento depositato in Corte di giustizia tributaria entro 60 giorni dalla notifica.",
     'correlate' => ['Diritto tributario']],
    ['l' => 'R', 'k' => 'rinuncia-eredita', 't' => "Rinuncia all'eredità", 'cat' => 'Successioni',
     'def' => "Atto unilaterale con cui il chiamato all'eredità dichiara di non volerla acquistare, evitando di subentrare nei debiti del defunto. Va resa con dichiarazione davanti al notaio o al cancelliere del tribunale entro 10 anni dall'apertura della successione.",
     'esempio' => "Eredità con debiti superiori all'attivo: il figlio rinuncia per non rispondere personalmente delle passività.",
     'correlate' => ['Successioni']],
    ['l' => 'R', 'k' => 'risarcimento-danno', 't' => 'Risarcimento del danno', 'cat' => 'Civile',
     'def' => "Obbligo di ripristinare la situazione patrimoniale o non patrimoniale lesa da un fatto illecito (artt. 2043 ss. c.c.) o da inadempimento contrattuale. Comprende danno emergente, lucro cessante, danno biologico, morale, esistenziale. Onere della prova sulla parte attrice.",
     'esempio' => "Incidente stradale con lesioni: la vittima ottiene risarcimento per spese mediche, lucro cessante e invalidità permanente.",
     'correlate' => ['Risarcimento danni']],
    ['l' => 'S', 'k' => 'separazione-consensuale', 't' => 'Separazione consensuale', 'cat' => 'Famiglia',
     'def' => "Procedura in cui i coniugi raggiungono un accordo sulle conseguenze della separazione (affidamento, mantenimento, casa, beni) e lo sottopongono al tribunale per omologa. Tempi rapidi (3-4 mesi); possibile anche via negoziazione assistita o davanti all'ufficiale di stato civile.",
     'esempio' => "Coppia con due figli: accordo su affido condiviso, mantenimento €700/mese, casa alla moglie. Omologa in 90 giorni.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'S', 'k' => 'separazione-giudiziale', 't' => 'Separazione giudiziale', 'cat' => 'Famiglia',
     'def' => "Procedura in cui non vi è accordo tra i coniugi e il tribunale decide con sentenza dopo un'istruttoria. Può essere accompagnata da addebito a uno dei coniugi (per violazione dei doveri matrimoniali), con conseguenze su mantenimento e successione.",
     'esempio' => "Marito addebitario per infedeltà ostentata e abbandono: perde diritto al mantenimento e alla successione.",
     'correlate' => ['Diritto di famiglia']],
    ['l' => 'S', 'k' => 'sentenza', 't' => 'Sentenza', 'cat' => 'Processo',
     'def' => "Provvedimento del giudice che decide la causa nel merito o su questioni processuali. Contiene fatti, motivazione e dispositivo. Diviene esecutiva immediatamente per quelle di condanna; passa in giudicato decorso il termine d'impugnazione.",
     'esempio' => "Sentenza di condanna al pagamento di €15.000: titolo esecutivo, possibile pignoramento anche prima del passaggio in giudicato.",
     'correlate' => ['Recupero crediti']],
    ['l' => 'S', 'k' => 'servitu', 't' => 'Servitù', 'cat' => 'Condominio',
     'def' => "Peso imposto su un fondo (servente) per l'utilità di un altro fondo (dominante) appartenente a diverso proprietario. Si costituisce per legge, contratto, usucapione o destinazione del padre di famiglia. Tipologie comuni: passaggio, acquedotto, veduta.",
     'esempio' => "Strada di accesso che attraversa il fondo del vicino: servitù di passaggio costituita per usucapione ventennale.",
     'correlate' => ['Diritto condominiale']],
    ['l' => 'S', 'k' => 'soccombenza', 't' => 'Soccombenza', 'cat' => 'Processo',
     'def' => "Posizione della parte che perde la causa o un singolo capo della stessa. Comporta, di regola, la condanna al rimborso delle spese processuali sostenute dalla controparte (avvocato, contributo unificato, CTU). Possibile la compensazione totale o parziale.",
     'esempio' => "Causa persa nel merito: condanna al pagamento di €4.500 di compensi e spese di lite.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'S', 'k' => 'stepchild-adoption', 't' => 'Stepchild adoption', 'cat' => 'Famiglia LGBTQ+',
     'def' => "Adozione del figlio del partner. In Italia ammessa per le coppie di fatto e omosessuali (unione civile) ai sensi dell'art. 44 lett. d) della legge 184/1983, secondo l'orientamento Cass. SU 33160/2023. Tutela il legame con entrambi i genitori sociali.",
     'esempio' => "Coppia di donne in unione civile: una è madre biologica, l'altra ottiene adozione del minore in casi particolari.",
     'correlate' => ['Famiglia LGBTQ+']],
    ['l' => 'S', 'k' => 'successione', 't' => 'Successione', 'cat' => 'Successioni',
     'def' => "Subentro di uno o più soggetti nella titolarità del patrimonio del defunto. Può essere legittima (per legge), testamentaria (per testamento) o necessaria (a tutela dei legittimari). Si apre con la morte del de cuius, a luogo dell'ultimo domicilio.",
     'esempio' => "Defunto senza testamento, lascia coniuge e due figli: successione legittima, eredità divisa tra moglie (1/3) e figli (2/3).",
     'correlate' => ['Successioni']],
    ['l' => 'T', 'k' => 'testamento', 't' => 'Testamento', 'cat' => 'Successioni',
     'def' => "Atto unilaterale revocabile con cui una persona dispone delle proprie sostanze per il tempo successivo alla morte. Le forme principali sono olografo (scritto a mano), pubblico (notaio + due testimoni), segreto. La capacità di testare richiede maggiore età e capacità di intendere.",
     'esempio' => "Testamento olografo che lascia la casa al figlio minore e l'auto alla figlia: validità subordinata al rispetto della legittima.",
     'correlate' => ['Successioni']],
    ['l' => 'T', 'k' => 'tfr', 't' => 'TFR', 'cat' => 'Lavoro',
     'def' => "Trattamento di Fine Rapporto: somma che spetta al lavoratore subordinato alla cessazione del rapporto, a qualsiasi titolo. Si calcola accantonando ogni anno la retribuzione divisa per 13,5, rivalutata. Possibili anticipazioni (casa, salute) in costanza di rapporto.",
     'esempio' => "Lavoratore dimissionario dopo 12 anni: TFR liquidato circa €18.000 lordi entro 30 giorni dalla cessazione.",
     'correlate' => ['Diritto del lavoro']],
    ['l' => 'U', 'k' => 'udienza', 't' => 'Udienza', 'cat' => 'Processo',
     'def' => "Comparizione delle parti davanti al giudice in un'aula del tribunale, in data prestabilita. Le tipologie principali sono prima udienza, istruttoria, di precisazione delle conclusioni, di discussione. Possibili oggi anche udienze cartolari (note scritte) o telematiche.",
     'esempio' => "Udienza istruttoria con escussione di tre testimoni: il giudice raccoglie le dichiarazioni a verbale.",
     'correlate' => ['Responsabilità civile']],
    ['l' => 'U', 'k' => 'unione-civile', 't' => 'Unione civile', 'cat' => 'Famiglia LGBTQ+',
     'def' => "Istituto introdotto dalla legge 76/2016 che disciplina la convivenza di due persone maggiorenni dello stesso sesso, con effetti analoghi al matrimonio in tema di obblighi reciproci, regime patrimoniale e successione.",
     'esempio' => "Costituzione davanti all'ufficiale di stato civile, dichiarazione congiunta di scelta del regime patrimoniale (comunione o separazione).",
     'correlate' => ['Famiglia LGBTQ+']],
    ['l' => 'U', 'k' => 'usufrutto', 't' => 'Usufrutto', 'cat' => 'Condominio',
     'def' => "Diritto reale di godere di un bene altrui ricavandone le utilità (frutti, abitazione, locazione) salvo rispettarne la destinazione. Si costituisce per contratto, testamento o legge; non può eccedere la vita del titolare e si estingue con la morte di questi.",
     'esempio' => "Genitore lascia in usufrutto al coniuge la casa coniugale e in nuda proprietà ai figli: il coniuge vi abita a vita.",
     'correlate' => ['Diritto condominiale']],
];

$sl_gloss_faq = [
    ['q' => "Qual è la differenza tra avvocato e procuratore?",
     'a' => "Nell'ordinamento italiano i due termini sono oggi sostanzialmente coincidenti: la figura del procuratore legale è stata abolita nel 1997 dalla legge 27. Si parla ancora di procuratore in senso processuale, per indicare l'avvocato che rappresenta la parte in giudizio in virtù di mandato (procura alle liti)."],
    ['q' => "Cos'è la prima udienza?",
     'a' => "È la prima comparizione delle parti davanti al giudice nel processo civile (art. 183 c.p.c.). In questa sede il giudice verifica la regolarità del contraddittorio, tenta la conciliazione, fissa il calendario delle attività istruttorie e i termini per il deposito delle memorie."],
    ['q' => "Cosa significa contumacia?",
     'a' => "È la mancata costituzione di una parte regolarmente citata in giudizio. Il processo prosegue ugualmente; le difese non svolte non possono più essere sollevate, salve le eccezioni rilevabili d'ufficio. La sentenza viene comunque notificata al contumace per far decorrere il termine breve d'impugnazione."],
    ['q' => "Quanto costa una causa?",
     'a' => "Le spese si articolano in onorari dell'avvocato (parametri ministeriali), contributo unificato (variabile per scaglione di valore), spese vive (CTU, notifiche, marca da bollo). In caso di soccombenza, il giudice condanna alle spese processuali. La prima consulenza in studio è gratuita."],
    ['q' => "Che differenza c'è tra ricorso e appello?",
     'a' => "Il ricorso è la forma generale dell'atto introduttivo o impugnatorio; l'appello è uno specifico mezzo di gravame contro le sentenze di primo grado. Si fa appello contro una sentenza di tribunale; si fa ricorso (ad esempio) per cassazione, al TAR, in materia tributaria o del lavoro."],
];

// Lettere presenti per la nav sticky.
$sl_gloss_letters_present = [];
foreach ($sl_gloss_terms as $sl_t) {
    $sl_gloss_letters_present[$sl_t['l']] = true;
}
$sl_gloss_az = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

// Raggruppa per lettera.
$sl_gloss_grouped = [];
foreach ($sl_gloss_terms as $sl_t) {
    $sl_gloss_grouped[$sl_t['l']][] = $sl_t;
}
ksort($sl_gloss_grouped);

// Counter editoriale "60 termini · 24 categorie" — il 24 cita le aree del
// diritto italiano coperte dal glossario in senso ampio (le label `cat` sono
// 9 macro-categorie raggruppanti, JSX usa il valore esteso 24).
$sl_gloss_cat_count = 24;

$sl_gloss_chain = function_exists('saltelli_get_breadcrumb_chain') ? saltelli_get_breadcrumb_chain() : [];
?>

<div class="sl-glossario">

    <section class="sl-glossario__hero" aria-labelledby="glossario-h1">
        <div class="sl-glossario__hero-grid">
            <div>
                <?php if (!empty($sl_gloss_chain) && count($sl_gloss_chain) > 1) : ?>
                    <nav class="sl-mono sl-page__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
                        <?php foreach ($sl_gloss_chain as $sl_idx => $sl_node) :
                            if ($sl_idx > 0) echo ' / ';
                            if (!empty($sl_node['url'])) : ?>
                                <a href="<?php echo esc_url($sl_node['url']); ?>"><?php echo esc_html($sl_node['name']); ?></a>
                            <?php else : ?>
                                <span><?php echo esc_html($sl_node['name']); ?></span>
                            <?php endif;
                        endforeach; ?>
                    </nav>
                <?php endif; ?>
                <div class="sl-mono sl-glossario__eyebrow">
                    <?php esc_html_e('§ Riferimenti · Glossario', 'saltelli'); ?>
                </div>
                <h1 class="sl-glossario__h1" id="glossario-h1">
                    <?php esc_html_e('Glossario', 'saltelli'); ?><br>
                    <em><?php esc_html_e('legale.', 'saltelli'); ?></em>
                </h1>
            </div>
            <div class="sl-glossario__hero-meta">
                <p class="sl-glossario__lede">
                    <?php esc_html_e('Sessanta termini essenziali del diritto italiano spiegati in linguaggio chiaro. Aggiornato a maggio 2026.', 'saltelli'); ?>
                </p>
                <div class="sl-mono sl-glossario__counter">
                    <?php
                    /* translators: 1 = number of terms, 2 = number of categories */
                    printf(
                        esc_html__('%1$d termini · %2$d categorie', 'saltelli'),
                        (int) count($sl_gloss_terms),
                        (int) $sl_gloss_cat_count
                    );
                    ?>
                </div>
            </div>
        </div>
    </section>

    <section class="sl-glossario__nav" aria-label="<?php esc_attr_e('Cerca e naviga il glossario', 'saltelli'); ?>">
        <div class="sl-glossario__nav-grid">
            <label class="screen-reader-text" for="sl-gloss-search"><?php esc_html_e('Cerca un termine', 'saltelli'); ?></label>
            <input type="search"
                   id="sl-gloss-search"
                   class="sl-glossario__search"
                   placeholder="<?php esc_attr_e('Cerca un termine — es. cartella, prescrizione, affidamento…', 'saltelli'); ?>"
                   autocomplete="off">
            <nav class="sl-glossario__az" aria-label="<?php esc_attr_e('Navigazione alfabetica', 'saltelli'); ?>">
                <?php foreach ($sl_gloss_az as $sl_L) :
                    if (isset($sl_gloss_letters_present[$sl_L])) : ?>
                        <a href="#sl-gloss-<?php echo esc_attr($sl_L); ?>"><?php echo esc_html($sl_L); ?></a>
                    <?php else : ?>
                        <span aria-hidden="true"><?php echo esc_html($sl_L); ?></span>
                    <?php endif;
                endforeach; ?>
            </nav>
        </div>
    </section>

    <section class="sl-glossario__list" aria-label="<?php esc_attr_e('Elenco termini', 'saltelli'); ?>">
        <p class="sl-glossario__empty" role="status" aria-live="polite">
            <?php esc_html_e('Nessun risultato. Prova un altro termine.', 'saltelli'); ?>
        </p>
        <?php foreach ($sl_gloss_grouped as $sl_letter => $sl_letter_terms) : ?>
            <div class="sl-glossario__group" id="sl-gloss-<?php echo esc_attr($sl_letter); ?>">
                <h2 class="sl-glossario__letter"><?php echo esc_html($sl_letter); ?></h2>
                <dl class="sl-glossario__dl">
                    <?php foreach ($sl_letter_terms as $sl_t) :
                        $sl_search_blob = strtolower($sl_t['t'] . ' ' . $sl_t['def'] . ' ' . $sl_t['cat']); ?>
                        <div class="sl-glossario__entry"
                             id="<?php echo esc_attr($sl_t['k']); ?>"
                             data-search="<?php echo esc_attr($sl_search_blob); ?>">
                            <dt class="sl-glossario__dt">
                                <span class="sl-mono sl-glossario__cat"><?php echo esc_html($sl_t['cat']); ?></span>
                                <span class="sl-glossario__term"><?php echo esc_html($sl_t['t']); ?></span>
                            </dt>
                            <dd class="sl-glossario__dd">
                                <p class="sl-glossario__def"><?php echo esc_html($sl_t['def']); ?></p>
                                <p class="sl-glossario__example">
                                    <strong><?php esc_html_e('Esempio.', 'saltelli'); ?></strong>
                                    <?php echo ' ' . esc_html($sl_t['esempio']); ?>
                                </p>
                                <?php if (!empty($sl_t['correlate'])) : ?>
                                    <div class="sl-glossario__related">
                                        <span class="sl-mono sl-glossario__related-label">
                                            <?php esc_html_e('Aree correlate:', 'saltelli'); ?>
                                        </span>
                                        <?php foreach ($sl_t['correlate'] as $sl_rel) :
                                            $sl_rel_url = $sl_gloss_cat_url($sl_rel); ?>
                                            <?php if ($sl_rel_url) : ?>
                                                <a class="sl-link" href="<?php echo esc_url($sl_rel_url); ?>"><?php echo esc_html($sl_rel); ?></a>
                                            <?php else : ?>
                                                <span><?php echo esc_html($sl_rel); ?></span>
                                            <?php endif;
                                        endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="sl-glossario__faq" aria-labelledby="glossario-faq-h">
        <div class="sl-glossario__faq-grid">
            <div class="sl-mono"><?php esc_html_e('§ Domande generali', 'saltelli'); ?></div>
            <div>
                <h2 class="sl-glossario__faq-h" id="glossario-faq-h">
                    <?php esc_html_e('Cinque chiarimenti.', 'saltelli'); ?>
                </h2>
                <div class="sl-faq">
                    <?php foreach ($sl_gloss_faq as $sl_f) : ?>
                        <details class="sl-faq__item">
                            <summary class="sl-faq__question"><?php echo esc_html($sl_f['q']); ?></summary>
                            <div class="sl-faq__answer"><p><?php echo esc_html($sl_f['a']); ?></p></div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="sl-glossario__cta" aria-labelledby="glossario-cta-h">
        <div class="sl-glossario__cta-grid">
            <div class="sl-mono"><?php esc_html_e('§ Manca un termine?', 'saltelli'); ?></div>
            <div>
                <h2 class="sl-glossario__cta-h" id="glossario-cta-h">
                    <?php esc_html_e('Non trovi un termine?', 'saltelli'); ?><br>
                    <em><?php esc_html_e('Scrivici.', 'saltelli'); ?></em>
                </h2>
                <p class="sl-glossario__cta-lede">
                    <?php esc_html_e('Il glossario è in continua espansione. Suggerisci un termine: lo aggiungeremo nella prossima revisione.', 'saltelli'); ?>
                </p>
                <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                    <span><?php esc_html_e('Suggerisci un termine', 'saltelli'); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

</div>

<?php
// === SCHEMA JSON-LD: DefinedTermSet × 60 + FAQPage ===
// Emessi inline a fine sezione (Google parses body+head). saltelli_emit_jsonld()
// usa ASCII-safe encoding per sopravvivere al round-trip DOMDocument di Iubenda.
$sl_gloss_url        = get_permalink();
$sl_gloss_studio     = function_exists('saltelli_studio_data') ? saltelli_studio_data() : ['legal_name' => 'Studio Legale Saltelli & Partners'];
$sl_gloss_termset_id = $sl_gloss_url . '#glossario';

$sl_gloss_terms_schema = [];
foreach ($sl_gloss_terms as $sl_t) {
    $sl_gloss_terms_schema[] = [
        '@type'            => 'DefinedTerm',
        '@id'              => $sl_gloss_url . '#' . $sl_t['k'],
        'name'             => $sl_t['t'],
        'description'      => $sl_t['def'],
        'inDefinedTermSet' => $sl_gloss_termset_id,
        'termCode'         => $sl_t['k'],
        'url'              => $sl_gloss_url . '#' . $sl_t['k'],
    ];
}

$sl_gloss_termset_schema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'DefinedTermSet',
    '@id'         => $sl_gloss_termset_id,
    'name'        => __('Glossario legale', 'saltelli'),
    'description' => __('Sessanta termini essenziali del diritto italiano, spiegati in linguaggio chiaro.', 'saltelli'),
    'url'         => $sl_gloss_url,
    'inLanguage'  => 'it-IT',
    'publisher'   => [
        '@type' => 'LegalService',
        'name'  => $sl_gloss_studio['legal_name'],
        'url'   => home_url('/'),
    ],
    'hasDefinedTerm' => $sl_gloss_terms_schema,
];

$sl_gloss_faq_main = [];
foreach ($sl_gloss_faq as $sl_f) {
    $sl_gloss_faq_main[] = [
        '@type'          => 'Question',
        'name'           => $sl_f['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => $sl_f['a'],
        ],
    ];
}
$sl_gloss_faq_schema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    '@id'        => $sl_gloss_url . '#faq',
    'url'        => $sl_gloss_url,
    'inLanguage' => 'it-IT',
    'isPartOf'   => ['@id' => $sl_gloss_termset_id],
    'mainEntity' => $sl_gloss_faq_main,
];

if (function_exists('saltelli_emit_jsonld')) {
    saltelli_emit_jsonld($sl_gloss_termset_schema);
    saltelli_emit_jsonld($sl_gloss_faq_schema);
}
?>

<script>
(function () {
    var input = document.getElementById('sl-gloss-search');
    if (!input) return;
    var list = document.querySelector('.sl-glossario__list');
    if (!list) return;
    var entries = list.querySelectorAll('.sl-glossario__entry');
    var groups  = list.querySelectorAll('.sl-glossario__group');
    input.addEventListener('input', function () {
        var q = input.value.trim().toLowerCase();
        var anyVisible = false;
        entries.forEach(function (e) {
            var match = !q || (e.dataset.search || '').indexOf(q) !== -1;
            e.style.display = match ? '' : 'none';
            if (match) anyVisible = true;
        });
        groups.forEach(function (g) {
            var visible = false;
            g.querySelectorAll('.sl-glossario__entry').forEach(function (e) {
                if (e.style.display !== 'none') visible = true;
            });
            g.style.display = visible ? '' : 'none';
        });
        list.dataset.empty = anyVisible ? 'false' : 'true';
    });
})();
</script>
