# Manaboom Affiliate WHMCS Addon Module #

## Summary ##
This addon module provides the basic tools needed for Mana Site Builder affiliates to sell websites with custom disk size, duration and price.
It provides both client and admin facing user interfaces.

For more information about WHMCS addon modules, please refer to the online documentation at
https://developers.whmcs.com/addon-modules/

## Features ##

- Includes all boom website templates.
- Ready to use sample products for ease of work.
- Customizable product disk, duration and price.
- An unlimited number of products.

## How to Install ##
Upload all files to your WHMCS directory.

Once uploaded go to your WHMCS administration area and find the addon manager under settings. Then Press the "Active" button.

## Configuration ##

After activation of the addon, the following fields must be filled:
"API Key": The affiliate API key.

"Group Code": Create a group for the products you want to sell as sitebuilder websites and enter its code here. (This plugin only works with products of this group)

"Default Template": First you need to enter the "API Key" once and click the "Save Changes" button to access the boom API and get a list of templates. After that, in this section, you will have a list of templates that you can choose as the default template for websites.

"Creat Sample Products": By checking this option, 3 sample products, having the required setup, will be created in the group that you entered in the "Group Code" section. You can edit, duplicate or delete these samples later.

## Products ##
Any sitebuilder product can be created in the Products section of WHMCS. Products made for this plugin must have the following features:

1- The group of products must be the group entered in the "Group Code" field of the config section.
2- The type of product must be "Shared Hosting".
3- The "Subdomain Options" feature of the product must be set to ".ov2.com". You can find this feature from the following path:
_"Setup" tab, products / Services → Products / Services, product selection, "Other" tab_
3- In the product editing section, the "Require Domain" option must be cheched.
4- The following 4 custom fields are required:
    A) Field Name: Disk
        Field Type: Drop Down
        Select Options: The amount of memory that this product (website) has.
        Check the "Required Field", "Show on Order Form" and "Show on Invoice" options.

    B) Field Name: Username
        Field Type: Text Box

    C) Field Name: Password
        Field Type: Text Box

    D) Field Name: Note
        Field Type: Text Box

    E) Field Name: Site title
        Field Name: Text Box
        Check the "Required Field", "Show on Order Form" and "Show on Invoice" options. 

## Personalization ##

Open up "/templates/six/"
You can edit the HTML and CSS properties of the following files in order to have your own template styles:
"manaaffiliate.tpl"
"manaaffiliateAdminDashboard.tpl"
"manaaffiliateSelectProduct.tpl"
"Mana_Affiliate/custom.css"

The following link can also be helpful for translating the product names:
https://help.whmcs.com/m/localisation/l/678185-translate-your-product-names-and-descriptions 