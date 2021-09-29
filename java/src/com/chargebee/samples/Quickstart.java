
<dependency>
  <groupId>com.chargebee</groupId>
  <artifactId>chargebee-java</artifactId>
  <version>LATEST</version>
</dependency>




implementation group: 'com.chargebee', name: 'chargebee-java', version: '{VERSION}'



Environment.configure("acmedoeswell-test","test_TsxhAdSxyvZ2Axio9YEj8rq24URhVkl3");
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
