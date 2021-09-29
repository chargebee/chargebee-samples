
require 'chargebee'
ChargeBee.configure(:site => "my-domain-test",
  :api_key => "test_YkiZnDgc1MWyjlWRNBJgHsKCRSSB8cuDlS")
result = ChargeBee::Customer.create({
  :id => "acme-east",
  :company => "Acme Eastern",
  :autoCollection => "on",
  :card => {
    :number => "4111111111111111",
    :expiry_month => 12,
    :expiry_year => 2022,
    :cvv => "100"
    }
  })
customer = result.customer
card = result.card



result = ChargeBee::ItemFamily.create({
  :id => "cloud-storage",
  :name => "Cloud Storage"
  })
item_family = result.item_family



result = ChargeBee::Item.create({
  :id => "silver-plan",
  :name => "Silver Plan",
  :type => "plan",
  :item_family_id => "cloud-storage"
  })
item = result.item



result = ChargeBee::ItemPrice.create({
  :id => "silver-plan-USD-monthly",
  :item_id => "silver-plan",
  :name => "Silver USD monthly",
  :pricing_model => "per_unit",
  :price => 50000,
  :external_name => "Silver USD",
  :period_unit => "month",
  :period => 1
  })
item_price = result.item_price



result = ChargeBee::Subscription.create_with_items("acme-east",{
  :subscription_items => [{
    :item_price_id => "silver-plan-USD-monthly",
    :quantity => 4
    }]
  })
subscription = result.subscription
customer = result.customer
card = result.card
invoice = result.invoice
unbilled_charges = result.unbilled_charges
