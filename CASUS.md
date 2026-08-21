# PizzaPlatform: Businesscase & Concept

> **Doel van dit document:** de casus zoals besproken op papier zetten, zodat deze doorgestuurd en gevalideerd kan worden. Onderaan staan de openstaande vragen en aannames die nog bevestigd moeten worden.

---

## 1. Het probleem

Heel veel pizzeria's in Nederland werken nu via **Thuisbezorgd** en staan daar **enorme commissies** af op elke bestelling. Alternatieven zoals **Sitedish** lossen dat maar half op:

- Ze leveren een bestelpagina op en dat is het: *"here's your page, succes ermee."*
- Geen begeleiding, geen doorontwikkeling, geen partnerschap.
- De pizzeria staat er na oplevering alleen voor.

**Dat mag bij dit platform absoluut niet gebeuren.** Het moet een Sitedish-achtig systeem zijn, maar dan véél beter: met echte ondersteuning en een verdienmodel dat niet op commissies leunt.

## 2. De kans

De klant achter dit platform:

- Bedrukt en levert **vrijwel alle pizzadozen van Nederland** aan alle grote partijen.
- Heeft momenteel **60 pizzeria's** onder zich.
- Heeft **150 pizzeria's "on hold"** staan die kunnen aansluiten.

Er is dus al een bestaand netwerk én een bestaand distributiekanaal (de dozen). Het platform hoeft niet koud de markt op, de klanten staan in de wachtrij.

## 3. Het concept

Een platform waar pizzeria's zich aanmelden via een **super slimme onboarding** en daarna een **eigen bestelomgeving** krijgen, gehost op een **subdomein van hun eigen website** (bijv. `bestellen.pizzeriamario.nl`).

### De slimme onboarding

De pizzeria doorloopt één flow waarin alles direct wordt ingericht:

1. **Naam & contactgegevens**
2. **Bedrijfsgegevens** (KvK, BTW, adres, openingstijden)
3. **Menu direct instellen** (producten, categorieën, prijzen, opties/extra's)
4. **Betalingen instellen** (online betalen direct gekoppeld)
5. **Subdomein & huisstijl** (eigen look op eigen subdomein)

Na de onboarding staat de bestelpagina live en kunnen klanten direct bestellen.

## 4. Het verdienmodel

**Geen commissie op bestellingen.** De winst zit in de pizzadozen:

### Dozen als kernmodel

- De pizzeria neemt de dozen af via het platform (de leverancier = wij).
- Marge: **± €0,39 per pizzadoos.**
- Hoe meer bestellingen via het platform, hoe meer dozen. Het platform verdient dus mee aan het succes van de pizzeria in plaats van eraan te knagen.

### Slimme reclame op de doos (up-sell)

De doos zelf wordt een marketingkanaal:

- **Partner-advertenties** op de doos (partnerpartijen betalen voor bedrukking/exposure).
- **"Claim je punt"-loyaliteit:** QR-code op de doos → klant scant → spaart punten → bij bijv. 10 punten een **gratis pizza**. Dit stuurt klanten terug naar de bestelpagina van de pizzeria (herhaalbestellingen) én maakt de doos interactief.

### Waarom dit werkt

| Partij | Wat ze krijgen |
|---|---|
| **Pizzeria** | Eigen bestelplatform zonder Thuisbezorgd-commissie, met échte ondersteuning, plus een loyaliteitsprogramma dat klanten laat terugkomen |
| **Klant (consument)** | Direct bestellen bij de pizzeria, punten sparen via de doos |
| **Platform (wij)** | Marge op elke doos + advertentie-inkomsten op de dozen |
| **Partners** | Advertentieruimte op miljoenen pizzadozen |

## 5. Wat het beter maakt dan Sitedish

| | Sitedish e.d. | Dit platform |
|---|---|---|
| Oplevering | Bestelpagina en klaar | Volledige onboarding + doorlopende ondersteuning |
| Verdienmodel | Abonnement/licentie | Dozen: platform verdient mee met de pizzeria, niet aan de pizzeria |
| Marketing | Niets | Slimme doos-reclame + loyaliteitsprogramma (QR / punten sparen) |
| Klantbinding | Geen | "Claim je punt" stuurt consumenten terug naar de bestelpagina |
| Hosting | Losse pagina | Subdomein van de eigen website van de pizzeria |

## 6. Hoe de geldstroom loopt

1. Consument bestelt op `bestellen.pizzeria-x.nl` en betaalt direct aan de pizzeria.
2. De pizzeria neemt pizzadozen af bij de leverancier (het platform).
3. Het platform maakt **± €0,39 marge per doos**.
4. Extra inkomsten: partneradvertenties op de dozen.
5. De QR op de doos voedt het loyaliteitsprogramma → meer herhaalbestellingen → meer dozen → meer marge.

## 7. Openstaande vragen / aannames om te valideren

Deze punten zijn nog **niet** bevestigd en moeten gecheckt worden bij de klant:

1. **Dozenafname:** is afname van dozen via het platform verplicht voor deelnemende pizzeria's, of vrijwillig? Hoe wordt dat contractueel geregeld?
2. **€0,39 marge:** klopt dit bedrag, en is dat marge per doos of de afdracht die de pizzeria betaalt?
3. **Betalingen:** welke PSP (Mollie, Stripe, Adyen…)? Betaalt de consument rechtstreeks aan de pizzeria of loopt het via het platform (split payments)?
4. **Abonnementskosten:** betaalt de pizzeria daarnaast nog een vast bedrag per maand, of is het model 100% dozen?
5. **Loyaliteit:** is "10 punten = gratis pizza" per pizzeria of platformbreed? Wie betaalt de gratis pizza: de pizzeria of het platform?
6. **Subdomein:** hebben alle pizzeria's al een eigen website/domein? Zo niet, levert het platform dan ook een hoofddomein/website?
7. **Bezorging:** regelt de pizzeria zelf de bezorging (aanname: ja), of moet het platform daar iets in faciliteren (bezorgzones, bezorgkosten, tijdsloten)?
8. **De 150 "on hold":** wat is de verwachte instroom/planning? Dit bepaalt hoe schaalbaar de onboarding vanaf dag één moet zijn.
9. **Kassa/POS-koppeling:** moeten bestellingen ook binnenkomen op een bonprinter/kassasysteem in de zaak?
10. **QR-tracking:** de QR-code op de doos moet uniek genoeg zijn om punten te koppelen aan een bestelling/klant. Hoe wordt fraude (meerdere scans, foto's delen) voorkomen?

---

*Opgesteld: 21 augustus 2026. Concept ter validatie, nog geen definitieve scope.*
