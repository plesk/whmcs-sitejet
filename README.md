# Sitejet by Plesk Provisioning Module for WHMCS Version 1.1 #

## Installation ##

- Extract the contents of the release zip file recursively in your WHMCS root folder. The module will be extracted to /modules/servers/sitejet and other folders won't be touched
- The module is now installed and can be used to configure new products
- Don't forget to remove the zip file afterwards

## Example Configuration ##

- Login to your WHMCS instance as an admin user
- Go to Configuration -> Sytem-Settings -> Products/Services
  - Create a new product group e.g. "Website Builders" with a nice headline like "Professional Website Builders for Web Design Agencies and Freelancers!" and "Standard Cart" template
  - Go back to Sytem-Settings -> Products/Services and create a new product called "Sitejet by Plesk" of type "Other" and select the just created product group there. Choose "Sitejet by Plesk Provisioning Module" as the module for automation. Keep the "Create as Hidden" switch set to "On" and click "Continue"
  - Switch to the tab "Module Settings" and fill the fields "Plesk KA Username" and "Plesk KA Password" with the credentials for the Plesk Partner API 3.0
  - Check "Automatically setup the product as soon as an order is placed" if you want a good user experience or any other option that matches your existing process 
  - Switch to the tab "Pricing" and select the payment type, e.g. "Recurring" and enable the "One Time/Monthly" option for selected currencies with the price you like to have as a "base fee". For a pay-as-you-grow product leave this at 0.00
  - Click on "Save Changes"
- Go to Configuration -> Sytem-Settings -> Configurable Options
  - Create a new group e.g. "Sitejet Options" and select the just created product in the "Assigned Products" list
  - Click on "Save Changes"
  - Add a new configurable option named "Package". For that enter "Package" to the field "Option Name" and "Business (1 website included)" to the field "Add Option", choose option type "Dropdown" and click on "Save Changes"
  - After saving enter "Agency (1 website included)" to the field "Add Option" and click again on "Save Changes"
  - Set the price that will be charged for every option value ("Business" and "Agency") on top of the base price defined in the product. If your base price was 0.00 you can just define the prices for the two packages here
  - Don't forget to "Save Changes" and close the window
  - Add another new configurable option named "Additional websites". For that enter "Additional websites" to the field "Option Name" AND "Add Option" (both fields), choose option type "Quantity" and click on "Save Changes"
  - After saving the options for "Minimum Quantity Required" and "Maximum Allowed" appear and can be set accordingly. 
  - Set the price that will be charged for every option value on top of the base and package price defined in the product.
  - Don't forget to "Save Changes" and close the window
  - IMPORTANT: There must only be two options, first "Package" and second "Additional websites" in this order and both are case sensitive
  - Click on "Save Changes"
- Go back to Configuration -> Sytem-Settings -> Products/Services
  - Edit the newly created product by clicking on the edit icon on the right
  - If you want a nice description with a logo for the product you can edit the "Product Description" and refer to the logo that has been delivered within the module's zip archive e.g.
    `Sitejet enables your team to deliver high-quality web design services profitably and at scale.<br/>`
    `<img height="50" src="./modules/servers/sitejet/sitejet.png">`
  - If you want the customer to be able to up/downgrade the product just go to the tab "Upgrades" and enable "Configurable Options"
  - Remove the check from "Hidden" to enable customers to buy the product
  - Click on "Save Changes"


## Example E-Mail Templates ##

The Sitejet license can be activated through the client area or alternatively by sending the activation link in the "New Product Information", which by default is the "Other Product/Service Welcome Email". To do so:
- Go to Configuration -> Sytem-Settings - Email Templates
- Edit the "Other Product/Service Welcome Email" in the "Product/Service Messags" group
- Add the placeholder `{$service_custom_field_activationlink}` to the template, e.g. `"If not already done please activate the product here: {$service_custom_field_activationlink}"`
- Click on "Save Changes"


## Troubleshooting ##

In case of problems please have a look at the "Module Log" by visiting Configuration -> System Logs and selecting "Module Log" on the left sidebar.

## Minimum Requirements ##

The Sitejet by Plesk Provisioning Module has been tested with WHMCS versions 7.8 and higher.

For the latest WHMCS minimum system requirements, please refer to
https://docs.whmcs.com/System_Requirements

## Copyright ##

Copyright 2022 [Plesk International GmbH](https://www.plesk.com)
