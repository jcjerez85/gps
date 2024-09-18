import requests

url_template = "https://knoxgps.com/admin/device_icons/destroy{}"

for number in range(7753, 99, -1):
    url = url_template.format(number)

    print(url)
    
    ##response = requests.delete(url)
    
    ##if response.status_code == 200:
    ##    print(f"Se eliminó correctamente el recurso {number}")
    ##else:
    ##    print(f"No se pudo eliminar el recurso {number}. Código de estado: {response.status_code}")

