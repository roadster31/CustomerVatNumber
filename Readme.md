# Customer Vat Number

Ce module permet à vos clients d'indiquer leur N° de TVA intracommunautaire au momùent de la création de leur compte, ou lors de la modification de leur profil.

Vous pouvez aussi modifier ou indiquer cette information dans la fiche du client dans le back-office.

Un boucle simple permet de récupérer le N° de TVA d'un client :

```
{loop type="customer-vat-number" name="vat" customer_id=<a customer ID>}
   VAT Number : {$VAT_NUMBER}
{/loop}
```

Attention : En front-office et en back-office, pour cause de manque de hooks, les champs de saisie et d'affichage
sont injectés dans le DOM en Javascript.

Si vous modifiez la structure du DOM des pages `register.html`, `account-update.html` et `account.html` et qu'elle devient différente 
du template pâr défaut, veillez à adapter le code JS qui injecte le HTML.

Idem pour le back-office dans les pages customers.html et customer-edit.html.
