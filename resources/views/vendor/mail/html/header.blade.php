@props([
    'url',
])
<!-- Логотип -->
<div>
<!--[if (gte mso 9)|(IE)]>
<table width="600" border="0" cellspacing="0" cellpadding="0" style="width: 600px;">
<tr>
<td>
<![endif]-->
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="max-width: 600px;">
<tr>
<td>
<div style="height: 18px; line-height: 30px; font-size: 28px;">&nbsp;</div>
<span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; color: #3c3c3c;">
<a href="{{ $url }}" target="_blank" style="text-decoration: none;">
<img width="40" height="40" alt="LOGO" border="0" style="display: inline-block;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAACfRJREFUeJztnW1wVNUZx3/nbhJEQCvvDu7e8GJ5GaxVEVuLaFudTqvgh476ocBY2qImd0lAS5RRMY4oIiWQu4lOGNtpi/UD0w9FRtspnbYopYCjZQrFWCC5G1ALBeyMEEiy9/TDJhLJJrtn9+69J8z9zWQms3vO+T93/3P2nj3nnvMIKSUh+mAEHYAn1AqDWnFJXMvgvwg7OptR0Z2Miu3Fjt0WdDiFIgbtV9Yr0Ql0Gi8gWACIXu9so8uIU93SGlBkBTH4DKmLDqXMWIrkSWB4P6XakdQTaX+OiuOf+RleoQwuQ2xzHgb1SMpzrHEMyUriyV/D4LjQwWFIQ/mNSLkByO8eIdmLIaqobN3lbWDeo7chTRNG01nyFJJKIFJgaxLJZiLGCipaPvEivGKgpyFNs0rpOFEBPAtc4XHrZ4B1DDfW8GDLOY/bLhj9DLHNOxHUA9OLrHQIWInlbCmyjhL6GGKbMxBsBO70WXk7kirizr981s2IPj8MDSbhvxmkNcXEAHQzok8PAUjE3gTxXZ9Vt2M5d/ms2S/69BAAIsuBTh8FuxBU+6iXFb0MsVo+QNLgn6CwqXQO+KeXHb0MAUgZtSBO+KB0is7Ucz7oKFFkQ4TIXuYiqls+BffpIgTzRSQrWdZ2Sr1iHtekQPEMsaO3k4i+T0NsrnLdcW2bQO4rQlQ9HOBU8lXlWvXlt5KIHSAR+04RYgKKMcqyr7kGIs/3mhZ/n3HJm7lPppTaaTTn4LKDL06te4TxTayWvyhVqRUGo2K7gVndr2wDsRSrtcXTyDxrqWnC5TSYNYjIQQQLufBB3sBx84fK7VU47yD4rWfxXWCLshkAI2M/4oIZAPeAPIBtrmHttBFeBeeNIbY5j86SA0jWkGmNQsrVNE2+Urld0bUcOFt4gJ9zji5jhXKttdNGIHg2wztDEdRweftBbHORF/eXwgyxy28gYf4VwdYsaxRj6eh8Urn9imNtCLk+7/j6IF/KayVx2NlVwPgBSkxA8EsSsb/TEL0l3+gg33tIXXQkZcYqxWnxDlKRr1B1pFlJa934YVw2pBmY0E8JCbzW/f8P6P+ec4xz56fy2CdnlPQbo1Nwjf3AkBxruEhew438lKoj/1HSQrWH1IoSGswllBjNSJaitkZRRiS1TkkP6P4AH+/n3X9gMBfLWYjlLMSQt4DYnbGkFDXKZgC4xgZyNwPAQLCQSOoDGswaameWqcjl3kMS5d8CuRGYqSKQQfJ7WK1vKdYR2LEdCOZ0v3AaqGVcMtFn9FYrDEbGFiDEOpBjul/dhZX8hvIybnop4I9qsfaJ/UMEy6hsfTOn0lkNsWOTEWI9ML+wwD7nIGVjrmfJu2pzVull3N1IXoeOR4l/PPCv+ZfNq3B5BskjSHcO8bY9SnrpRbJ9eLcusxUplxNPHh6oUPavLCFq8M4MgOl0nqhUrlXZ+h4yNZG4syirGQCPOKepdKqQqUnKZgB0HLfwdpFsfvdnOSDBzGVJVmFfPSZ7wYuIHz3qS5266EgQ6qNCDwhqcvFLiLJM43o9KDOeB0YGIR3kbO8S6s2bAtTPjD3peiQ/Dko+SEMMBBuKPXuqjOjaQOGPHOVNsOshgjnYse8HGkNvEuZ9IO4IMoSgF6jOIuTYgGPohRyDt3NnygRliAS2ICIzsJKNAcXQFyvZCPJaoAlwgwjBf0Mke4HbsJz7qTzi+K6fDSv5EZbzEMhbkLzjt7yfhhxD8BCnkl/Dcnb6qJsfVvJd4sm5wP0IWv2SLfFLCMOYpfNDzpmREostNE58G+l+7Ieifz3kfFeHb1pe42PsQY+yQi4iNEQzQkM0IzREM0JDNCM0RDP02h8SEvYQ3QgN0YzQEM0IDdGM0BDNCA3RjNAQzQgN0YzQEM0IDdGM0BDNyL6mnjCbgJ8UrNTpjspvX7gG1EVHUmqc9KClTVjOkoEKhD1EM0JDNCM0RDNCQzQjNEQzQkM0wz9DhpQo7dfWCh9j988Q1303fR7IIMM25yFd307E9vMrq+c8kD+TiF7no25+bJw0Fbv8rRzOcfEU/55+/xxxB4j3SJg/R6RWUnnUi1/A3tE0+UrOdz1BhGWA71+zQd3US4AlyMgBErEHAoqhL4nYA3R0NSOoIQAzIPhR1jiE4dnhXwUjxRXAuCBDCNqQ9xnr/CLgGC5wKvlq95a7wAjSEImUVcpnMRaTVdLFEFWkN6UGQnCGCH5DPPl2YPr9Udm6C+TrQckHZchZUiUrA9LOTolcAQSSuyqg04DkapYeTirXWzd+mC91Hm47BnKNcj0PyG6IlC8CWz3UPMKIiPrBlo3l07lsyDFscw2NY/vLznaBuuhQGswaLhvSRn1M/RS84ZGfAUeU6/XP1u7PckCyGxJPHsZy7gXxbeCfBYcl5PK8Ug2lWA9ciaAGd+jAx7La5jxKjZ5ja6/CEHXKeg+2nAP5qHK9PogPEeJuLOfebKfJger+kFpRwujYYlxWIxitHJvkT8Qd9aQtidh8EL/L0N5epKhmaevfAKg3b8KgjkzZ3CTziTtvqGubvwfyOVr8UwRr+O+IOlbtz3lbtZ/HxHaBeyNWm1ovs68dgujYD0zpp4QLbO7+fwH99/pDyLKZxP99Xkm/sXw6rtwHlOZYI31MbGnpYzx86LiSFvne1Je1naLSqcIwZgE7cqzVoGwGgOhYRv9mQPoaFnX/DXQ9UzA61JO3VLQeRPByjqX3INxbiTuL8jEDvNrSlj0D50lEaqryRGLjxPG4bjPepc77DORUrORHSrVeNq8iJZp7HTt7MZ5lFPVm2Bt33qDDnYHgcTKN3yVP5DWr67ov4W0ew+FIoT6cfcQ5jZCZDsVsR/IiRvs04s6vvEjvqm+6iobyryPlTrxPVyExmEuFo3b0kk/pKoq3C9eO3o4wNiBkFZXJXO8zadIXvwuYXZzgeI+TyZtZJdUOKasvvxVDbgK5HCv5h2IEVuRt0ULk1Y3t8sUIqZ4BRwm5GCuZx0xznteUa+va7VNfO20El7c3A1cXWek4ZSVfZsnh/xVZR4mg10P6MvTcMxTfDICxdHY+5YOOEnr1EPVcHYXSSSpynXJOkyKiVw9xRT3+mQFQSiRl+6iXFX0MaTDvCSAPLsBd2OV3B6CbEX0McTkCbA9AeTtIT39LFIJe9xDoyWqzEZhRZCUtE9zr00N6iDvbKRvzVaAaKMaQ9AxQy3DjOt3MAB17SG8arhkFkacVp/n7QyLZTMRYofP5wXob0kM6/9QGMi085cYehKhOP1GiN4PDkB6yT/NfjGfT4n4xuAyB9MMLZcZSJE+SKc1rmnYk9UTan6PieCCP8+TL4DOkh1eiE+g0Xug1zd/DNrqMeF4pVjVg8BrSgx2djejOxilltZZPQyow+A2B9PoJoLy+oSGXhiGXEP8HkGNM9lLTXUUAAAAASUVORK5CYII=">
<span style="display:inline-block;vertical-align: top; line-height: 40px; font-family: Arial, Helvetica, sans-serif; font-size: 24px; color: #3c3c3c; font-weight: bold;">{{ $slot }}</span>
</a>
</span>
</td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</div>
<!-- Логотип END -->
