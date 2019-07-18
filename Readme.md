# Customer Vat Number

Ce module permet à vos clients d'indiquer leur N° de TVA intracommunautaire au momùent de la création de leur compte, ou
lors de la modification de leur profil. Le numéro indiqué par le client est vérifié via une expression régulière pour
s'assurer que son format est valide (voir https://www.oreilly.com/library/view/regular-expressions-cookbook/9781449327453/ch04s21.html)

Vous pouvez aussi modifier ou indiquer cette information dans la fiche du client dans le back-office.

Un boucle simple permet de récupérer le N° de TVA d'un client :

```
{loop type="customer-vat-number" name="vat" customer_id=<a customer ID>}
   VAT Number : {$VAT_NUMBER}
{/loop}
```

## Intégration 

Le module utilise des hooks pour s'intégrer en front-office, en back-offcie et dans la facture PDF. Il n'y a rien de
particuler à faire.

Attention : en front-office et en back-office, pour cause de manque de hooks, les champs de saisie et d'affichage
sont injectés dans le DOM en Javascript. Si vous modifiez la structure du DOM des pages `register.html`, `account-update.html` 
et `account.html` et qu'elle devient différente du template pâr défaut, veillez à adapter le code JS qui injecte le HTML.

Idem pour le back-office dans les pages customers.html et customer-edit.html.
