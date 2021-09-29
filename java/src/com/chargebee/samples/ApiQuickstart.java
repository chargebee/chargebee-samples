
Environment.configure("my-domain-test","test_YkiZnDgc1MWyjlWRNBJgHsKCRSSB8cuDlS");
Result result = Customer.create()
.id("acme-east")
.company("Acme Eastern")
.autoCollection(on)
.cardNumber(4111111111111111)
.cardCvv(100)
.cardExpiryMonth(12)
.cardExpiryYear(2022)
.request();
Customer customer = result.customer();
Card card = result.card();



Result result = ItemFamily.create()
.id("cloud-storage")
.name("Cloud Storage")
.request();
ItemFamily itemFamily = result.itemFamily();



Result result = Item.create()
.id("silver-plan")
.name("Silver Plan")
.type(Type.PLAN)
.itemFamilyId("cloud-storage")
.request();
Item item = result.item();



Result result = ItemPrice.create()
.id("silver-plan-USD-monthly")
.itemId("silver-plan")
.name("Silver USD monthly")
.pricingModel(PricingModel.PER_UNIT)
.price(50000)
.externalName("Silver USD")
.periodUnit(PeriodUnit.MONTH)
.period(1)
.request();
ItemPrice itemPrice = result.itemPrice();



Result result = Subscription.createWithItems("acme-east")
.subscriptionItemItemPriceId(0,"silver-plan-USD-monthly")
.subscriptionItemQuantity(0,4)
.request();
Subscription subscription = result.subscription();
Customer customer = result.customer();
Card card = result.card();
Invoice invoice = result.invoice();
List<UnbilledCharge> unbilledCharges = result.unbilledCharges();


